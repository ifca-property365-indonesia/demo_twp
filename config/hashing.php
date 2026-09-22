<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | Password akun portal (tabel all_login) memakai Argon2id: memory-hard,
    | jauh lebih mahal untuk di-brute force dengan GPU/ASIC dibanding bcrypt,
    | dan jadi rekomendasi pertama OWASP untuk password storage.
    |
    | Pilihan: "bcrypt", "argon" (argon2i), "argon2id".
    |
    | Kalau PHP di server tidak dikompilasi dengan dukungan Argon2,
    | App\Support\Password otomatis turun ke bcrypt supaya login tetap jalan.
    |
    */

    'driver' => env('HASH_DRIVER', 'argon2id'),

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | Dipakai untuk memverifikasi hash lama yang sudah terlanjur bcrypt, dan
    | sebagai cadangan kalau Argon2 tidak tersedia.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => env('HASH_VERIFY', true),
        'limit' => env('BCRYPT_LIMIT', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options
    |--------------------------------------------------------------------------
    |
    | Default: 64 MB memori, 4 iterasi, 1 thread (~130 ms per hash di server ini).
    | Semuanya di atas batas minimum OWASP (19 MB / 2 iterasi). Kalau server
    | kekurangan memori saat banyak login bersamaan, turunkan lewat .env
    | (mis. ARGON_MEMORY=19456 dan ARGON_TIME=2).
    |
    */

    'argon' => [
        'memory' => env('ARGON_MEMORY', 65536),
        'threads' => env('ARGON_THREADS', 1),
        'time' => env('ARGON_TIME', 4),
        'verify' => env('HASH_VERIFY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rehash On Login
    |--------------------------------------------------------------------------
    */

    'rehash_on_login' => true,

];
