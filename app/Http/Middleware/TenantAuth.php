<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Proteksi halaman portal Tenant (dulu: webtenant Authcheck).
 * Login tenant menyimpan flag session 'is_Tenant_logged'.
 */
class TenantAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('is_Tenant_logged')) {
            return redirect('/')->with('alert', 'Please login first!');
        }

        return $next($request);
    }
}