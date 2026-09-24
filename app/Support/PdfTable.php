<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf;

/**
 * PDF laporan tabel (view pdf.table) yang tampil di tab baru (inline), sama untuk semua
 * tombol Generate PDF admin. Tabel lebar (>= 7 kolom) otomatis landscape, seperti tombol
 * PDF DataTables tenant (assets/app/js/pdf-export.js).
 */
class PdfTable
{
    public const LANDSCAPE_FROM_COLUMNS = 7;

    /**
     * @param string $filename nama file tanpa .pdf (dipakai saat diunduh dari viewer)
     * @param array  $data     isi view pdf.table: title, columns, rows, filters?, disclaimer?
     */
    public static function stream(string $filename, array $data)
    {
        $data += ['filters' => [], 'rows' => [], 'disclaimer' => null];
        $orientation = count($data['columns']) >= self::LANDSCAPE_FROM_COLUMNS ? 'landscape' : 'portrait';

        $dompdf = Pdf::loadView('pdf.table', $data)
            ->setPaper('a4', $orientation)
            ->setOption('isFontSubsettingEnabled', true)
            ->getDomPDF();
        $dompdf->render();

        // kaki halaman: judul di kiri, "Halaman x dari y" di kanan
        $canvas = $dompdf->getCanvas();
        $metrics = $dompdf->getFontMetrics();
        $font = $metrics->getFont('DejaVu Sans');
        $size = 7;
        $color = [0.42, 0.45, 0.5];
        $y = $canvas->get_height() - 26;
        $page = __('common.pdf_page', ['page' => '{PAGE_NUM}', 'total' => '{PAGE_COUNT}']);
        $canvas->page_text(28, $y, $data['title'], $font, $size, $color);
        $canvas->page_text($canvas->get_width() - 28 - $metrics->getTextWidth($page, $font, $size), $y, $page, $font, $size, $color);

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"',
        ]);
    }
}
