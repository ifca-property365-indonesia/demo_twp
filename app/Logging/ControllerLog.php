<?php

namespace App\Logging;

use Illuminate\Support\Facades\File;
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

    /** Lama simpan log (hari), config logging.channels.controller.days (LOG_KEEP_DAYS, default 60). */
    public static function keepDays(): int
    {
        return max(1, (int) config('logging.channels.controller.days', 60));
    }

    /**
     * Hapus folder log harian (storage/logs/<yyyy-mm-dd>) yang lebih tua dari $days hari.
     * Hanya folder bernama tanggal yang disentuh (laravel.log dll. dibiarkan).
     *
     * @return string[] nama folder yang dihapus
     */
    public static function prune(?int $days = null): array
    {
        $days ??= static::keepDays();
        $limit = date('Y-m-d', strtotime('-' . $days . ' days'));
        $deleted = [];

        foreach (glob(storage_path('logs/*'), GLOB_ONLYDIR) ?: [] as $dir) {
            $name = basename($dir);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $name) && $name < $limit) {
                if (File::deleteDirectory($dir)) {
                    $deleted[] = $name;
                }
            }
        }

        return $deleted;
    }
}
