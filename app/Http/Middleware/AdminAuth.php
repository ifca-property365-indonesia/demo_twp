<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Proteksi halaman portal Admin (dulu: webadmin AuthCheck).
 * Login admin menyimpan flag session 'is_login'.
 */
class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('is_login')) {
            return redirect('/')->with('alert', 'Please login first!');
        }

        return $next($request);
    }
}