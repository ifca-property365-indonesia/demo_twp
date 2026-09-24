<?php

namespace App\Support;

/**
 * Tanggal dari form. Semua pemilihan tanggal di portal memakai bootstrap-datepicker
 * dengan format dd/mm/yyyy (sama dengan History Invoice).
 *
 * dd/mm/yyyy diurai manual karena strtotime membaca '01/09/2026' sebagai m/d/Y
 * (9 Januari) dan '24/09/2026' sebagai tidak valid. yyyy-mm-dd juga diterima.
 */
class DateInput
{
    /** Tanggal form -> string dengan $format (default Y-m-d); null kalau kosong / tidak valid. */
    public static function format($value, $format = 'Y-m-d')
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})#', $value, $m)) {
            if (!checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
                return null;
            }
            return date($format, mktime(0, 0, 0, (int) $m[2], (int) $m[1], (int) $m[3]));
        }

        $time = strtotime($value);

        return $time === false ? null : date($format, $time);
    }

    /** Tanggal database / Y-m-d -> dd/mm/yyyy untuk mengisi field datepicker ('' kalau kosong). */
    public static function toDmy($value)
    {
        return $value ? date('d/m/Y', strtotime($value)) : '';
    }
}
