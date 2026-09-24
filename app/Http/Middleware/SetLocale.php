<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Bahasa tampilan (lang/{en,id}/...) dari session 'locale', selain itu default
 * config('app.locale') = English. Session 'locale' diisi saat login dari pilihan user
 * tersimpan (App\Support\UserLocale) dan saat user mengganti bahasa (/language/{locale}).
 * Pilihan disimpan per user, bukan per browser, jadi user baru selalu mulai dari English.
 */
class SetLocale
{
    /** Bahasa yang tersedia: kode -> nama (ditulis dalam bahasanya sendiri). */
    public const LOCALES = [
        'en' => 'English',
        'id' => 'Bahasa Indonesia',
    ];

    public function handle(Request $request, Closure $next)
    {
        $locale = $request->session()->get('locale');

        // Selalu di-set (juga ke default) supaya bahasa request sebelumnya tidak terbawa
        // kalau proses PHP dipakai ulang untuk beberapa request.
        app()->setLocale(isset(self::LOCALES[$locale]) ? $locale : config('app.locale'));

        return $next($request);
    }
}
