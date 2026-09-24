<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Gambar News & Promo (tabel newsfeed.picture), file di images/newspromo/.
 *
 * Kolom picture sekarang berisi path relatif "images/newspromo/<file>". Data lama berisi URL
 * absolut dari aplikasi/host lain (mis. .../webadmin/images/newspromo/x.jpg atau
 * .../admin/images/newspromo/x.jpg) -> url() mengambil nama filenya saja supaya tetap tampil.
 */
class NewsPicture
{
    public const DIR = 'images/newspromo';

    /** Simpan file upload, kembalikan path relatif untuk kolom picture */
    public static function store(UploadedFile $file): string
    {
        $dir = base_path(self::DIR);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // nama unik supaya file lain dengan nama sama tidak tertimpa
        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '_') ?: 'news';
        $name = date('YmdHis') . '_' . Str::limit($base, 60, '') . '.' . strtolower($file->getClientOriginalExtension());
        $file->move($dir, $name);

        return self::DIR . '/' . $name;
    }

    /** URL untuk <img src>; null kalau kosong */
    public static function url(?string $stored): ?string
    {
        $stored = trim((string) $stored);
        if ($stored === '') {
            return null;
        }

        $pos = strpos($stored, self::DIR . '/');
        if ($pos !== false) {
            return url(self::DIR . '/' . rawurlencode(basename(rawurldecode(substr($stored, $pos)))));
        }

        // gambar dari luar (bukan folder newspromo) dibiarkan apa adanya
        return preg_match('#^https?://#i', $stored) ? $stored : url($stored);
    }
}
