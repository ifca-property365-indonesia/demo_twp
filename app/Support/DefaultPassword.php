<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Password default untuk akun baru / reset password, diambil dari tabel `defaultpassword`
 * (diatur admin lewat menu System Spec -> Default Password). Kalau tabel kosong,
 * dipakai nilai lama 'cartenz123'.
 */
class DefaultPassword
{
    const FALLBACK = 'cartenz123';

    /** Password default dalam bentuk plain text. */
    public static function get()
    {
        $value = DB::connection('ifcaadm')->table('defaultpassword')->value('password');
        $value = is_string($value) ? trim($value) : '';
        return $value !== '' ? $value : self::FALLBACK;
    }

    /** Password default dalam bentuk md5 (format yang disimpan di all_login). */
    public static function hash()
    {
        return md5(self::get());
    }
}
