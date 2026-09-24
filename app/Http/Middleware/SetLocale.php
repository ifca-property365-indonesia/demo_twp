<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Bahasa tampilan (label menu di lang/{en,id}/{admin,tenant}.php) dari pilihan user:
 * session 'locale', lalu cookie 'locale' (supaya tetap diingat setelah logout),
 * selain itu default config('app.locale'). Diganti lewat route /language/{locale}.
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
        $locale = $request->session()->get('locale') ?: $request->cookie('locale');

        // Selalu di-set (juga ke default) supaya bahasa request sebelumnya tidak terbawa
        // kalau proses PHP dipakai ulang untuk beberapa request.
        app()->setLocale(isset(self::LOCALES[$locale]) ? $locale : config('app.locale'));

        return $next($request);
    }
}
