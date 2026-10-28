<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Potong teks supaya muat di kolom tujuan sebelum insert / update, alih-alih gagal dengan
 * "String or binary data would be truncated" (SQL Server). Panjang kolom dibaca dari
 * INFORMATION_SCHEMA (sekali per tabel per request). Setiap pemotongan dicatat sebagai warning.
 *
 *   $row = ColumnFit::fit('dblive', 'mgr.sv_entry_multi_dt', $row);
 */
class ColumnFit
{
    /** @var array<string, array<string, int>> */
    private static array $lengths = [];

    public static function fit(string $connection, string $table, array $row): array
    {
        $lengths = self::lengths($connection, $table);
        $cut = [];

        foreach ($row as $column => $value) {
            $max = $lengths[strtolower((string) $column)] ?? null;
            if ($max === null || !is_string($value) || mb_strlen($value) <= $max) {
                continue;
            }
            $row[$column] = mb_substr($value, 0, $max);
            $cut[$column] = ['max' => $max, 'asli' => $value];
        }

        if ($cut) {
            Log::warning("Data dipotong agar muat di {$table}", $cut);
        }

        return $row;
    }

    /** Panjang maksimum kolom teks (kolom MAX / non-teks tidak ikut). */
    private static function lengths(string $connection, string $table): array
    {
        $key = $connection . '|' . strtolower($table);
        if (!isset(self::$lengths[$key])) {
            [$schema, $name] = str_contains($table, '.') ? explode('.', $table, 2) : ['dbo', $table];
            self::$lengths[$key] = [];
            foreach (DB::connection($connection)->select(
                'SELECT COLUMN_NAME, CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CHARACTER_MAXIMUM_LENGTH > 0',
                [$schema, $name]
            ) as $col) {
                self::$lengths[$key][strtolower($col->COLUMN_NAME)] = (int) $col->CHARACTER_MAXIMUM_LENGTH;
            }
        }

        return self::$lengths[$key];
    }
}
