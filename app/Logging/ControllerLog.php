<?php

namespace App\Logging;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Lokasi file log per hari per controller:
 *   storage/logs/<yyyy-mm-dd>/<namespace>/<controller>.log
 * mis. Admin\PermitController  -> storage/logs/2026-10-06/admin/permit.log
 *      Tenant\PermitController -> storage/logs/2026-10-06/tenant/permit.log
 *      PortalLoginController   -> storage/logs/2026-10-06/portal_login.log
 * Di luar request controller (artisan, route closure, 404) -> storage/logs/<yyyy-mm-dd>/system.log.
 */
class ControllerLog
{
    public static function folder(): string
    {
        $class = null;
        try {
            $class = Route::current()?->getControllerClass();
        } catch (\Throwable $e) {
            // belum ada router (mis. saat boot) -> system
        }

        $prefix = 'App\\Http\\Controllers\\';
        if (!$class || !str_starts_with($class, $prefix)) {
            return 'system';
        }

        $parts = explode('\\', substr($class, strlen($prefix)));
        $name = preg_replace('/Controller$/', '', array_pop($parts));
        $parts[] = $name;

        return implode('/', array_map(fn ($p) => Str::snake($p), $parts));
    }

    public static function path(): string
    {
        return storage_path('logs/' . date('Y-m-d') . '/' . static::folder() . '.log');
    }
}
