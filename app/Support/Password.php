<?php

namespace App\Support;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Hash;

/**
 * Hashing password akun portal (tabel all_login).
 *
 * Password baru disimpan memakai driver di config/hashing.php (default Argon2id,
 * turun otomatis ke bcrypt kalau PHP server tidak mendukung Argon2).
 *
 * Saat login, hash yang tersimpan bisa tiga bentuk dan ketiganya dicek bergantian:
 *   - $argon2id$... / $argon2i$...  -> Argon2 (format baru)
 *   - $2y$...                       -> bcrypt (akun yang sempat pakai bcrypt)
 *   - 32 karakter hex               -> md5 (akun lama yang belum pernah ganti password)
 * Algoritmanya ditentukan dari prefix hash, bukan dari driver aktif, karena
 * hasher Argon2 milik Laravel menolak (melempar exception) hash bcrypt dan sebaliknya.
 *
 * Begitu sebuah akun berhasil login dengan hash lama, password-nya langsung ditulis
 * ulang ke format terbaru lewat upgrade(), jadi semua akun pindah sendiri tanpa
 * perlu user mengganti password.
 *
 * Kolom all_login.password harus cukup lebar (Argon2id ~97 karakter, bcrypt 60);
 * lihat migration 2026_09_22_000000_widen_all_login_password.
 */
class Password
{
    /** Hash untuk disimpan ke database (driver aktif: Argon2id / bcrypt). */
    public static function make($plain)
    {
        return Hash::driver(self::driver())->make(self::normalize($plain));
    }

    /**
     * Cocokkan password yang diketik user dengan hash yang tersimpan.
     * Menerima Argon2, bcrypt, maupun md5 (akun lama yang belum ganti password).
     */
    public static function check($plain, $stored)
    {
        $stored = (string) $stored;

        if ($stored === '') {
            return false;
        }

        if (self::isLegacy($stored)) {
            return hash_equals(strtolower($stored), md5(self::normalize($plain)));
        }

        $driver = self::driverFor($stored);

        if ($driver === null) {
            // Format tidak dikenali (mis. plain text sisa data lama).
            return false;
        }

        try {
            return Hash::driver($driver)->check(self::normalize($plain), $stored);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Hash lama (md5, 32 karakter hex)? */
    public static function isLegacy($stored)
    {
        return (bool) preg_match('/^[a-f0-9]{32}$/i', (string) $stored);
    }

    /** Perlu ditulis ulang (md5, format tidak dikenali, algoritma lama, atau cost lama)? */
    public static function needsUpgrade($stored)
    {
        $stored = (string) $stored;

        if (self::isLegacy($stored)) {
            return true;
        }

        $driver = self::driverFor($stored);

        if ($driver === null || $driver !== self::driver()) {
            return true;
        }

        try {
            return Hash::driver($driver)->needsRehash($stored);
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * Dipanggil setelah password terbukti cocok: kalau hash tersimpan masih format
     * lama (md5/bcrypt), baris all_login yang ditunjuk $query ditulis ulang.
     * $query harus sudah di-where ke satu baris (mis. ->where('id', $row->id)).
     */
    public static function upgrade(Builder $query, $stored, $plain)
    {
        if (!self::needsUpgrade($stored)) {
            return false;
        }

        try {
            $query->update(array('password' => self::make($plain)));
            return true;
        } catch (\Throwable $e) {
            // Gagal upgrade (mis. kolom password belum dilebarkan) tidak boleh
            // menggagalkan login yang password-nya sudah benar.
            report($e);
            return false;
        }
    }

    /**
     * Driver yang dipakai untuk hash baru. Argon2 dipakai hanya kalau PHP di server
     * ini mendukungnya; kalau tidak, dipakai bcrypt supaya login tidak ikut mati.
     */
    public static function driver()
    {
        $driver = (string) config('hashing.driver', 'bcrypt');

        if (self::isArgon($driver) && !self::argonSupported($driver)) {
            return 'bcrypt';
        }

        return $driver;
    }

    /** Nama driver Laravel untuk sebuah hash, dilihat dari prefix-nya. */
    private static function driverFor($stored)
    {
        if (str_starts_with($stored, '$argon2id$')) {
            return 'argon2id';
        }

        if (str_starts_with($stored, '$argon2i$')) {
            return 'argon';   // nama driver argon2i di Laravel
        }

        if (preg_match('/^\$2[abxy]?\$/', $stored)) {
            return 'bcrypt';
        }

        return null;
    }

    private static function isArgon($driver)
    {
        return $driver === 'argon' || $driver === 'argon2id';
    }

    private static function argonSupported($driver)
    {
        $algo = $driver === 'argon2id' ? 'argon2id' : 'argon2i';

        return function_exists('password_algos') && in_array($algo, password_algos(), true);
    }

    private static function normalize($plain)
    {
        return trim((string) $plain);
    }
}
