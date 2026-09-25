<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Sumber data semua tabel ticket (dasbor & history, tenant & admin): mgr.sv_entry_hd.
 *
 * Setiap baris = satu work order (report_no WOyymmnnnn), termasuk WO yang dibuat
 * langsung di sistem IFCA desktop. Ticket yang belum punya baris HD tidak ikut tampil.
 * Insert/update ke sv_entry_multi (MySQL & SQL Server) tetap berjalan seperti biasa;
 * lihat TicketController::update (blok "insert ke HD").
 *
 * Kolom hasil (alias t): report_no, entity_cd, project_no, debtor_acct, reported_date,
 * work_requested, serv_req_by, lot_no, status, complain_no, category_cd, category_desc, name.
 */
class TicketHd
{
    /**
     * Query builder (koneksi dblive) yang siap diberi where / orderBy memakai alias "t.".
     *
     * category_cd: dari HD; kalau kosong (WO lama) diambil dari ticket penghubungnya,
     * yaitu sv_entry_multi dengan complain_no = hd.note1 (ticket TWP baru) atau
     * sv_entry_multi_dt.report_no = hd.report_no (cara sistem desktop).
     */
    public static function query()
    {
        $db = DB::connection('dblive');

        $linkedComplainNo = "COALESCE(hd.note1, (
                SELECT TOP 1 d.complain_no FROM mgr.sv_entry_multi_dt d
                WHERE d.entity_cd = hd.entity_cd AND d.project_no = hd.project_no AND d.report_no = hd.report_no
            ))";

        $hd = $db->table('mgr.sv_entry_hd as hd')->select(
            'hd.report_no',
            'hd.entity_cd',
            'hd.project_no',
            'hd.debtor_acct',
            'hd.reported_date',
            'hd.work_requested',
            'hd.serv_req_by',
            'hd.lot_no',
            'hd.status',
            DB::raw($linkedComplainNo . ' AS complain_no'),
            DB::raw("COALESCE(NULLIF(hd.category_cd, ''), (
                SELECT TOP 1 m.category_cd FROM mgr.sv_entry_multi m
                WHERE m.entity_cd = hd.entity_cd AND m.project_no = hd.project_no
                  AND m.complain_no = " . $linkedComplainNo . "
            )) AS category_cd")
        );

        return $db->query()
            ->fromSub($hd, 't')
            ->leftJoin('mgr.sv_category as cat', 'cat.category_cd', '=', 't.category_cd')
            ->leftJoin('mgr.ar_debtor as deb', function ($join) {
                $join->on('deb.entity_cd', '=', 't.entity_cd')
                    ->on('deb.project_no', '=', 't.project_no')
                    ->on('deb.debtor_acct', '=', 't.debtor_acct');
            })
            ->select('t.*', 'cat.descs as category_desc', 'deb.name');
    }

    /**
     * Isi $row->picture_url (null kalau tidak ada) untuk baris hasil query(): gambar ticket untuk
     * tombol "lihat gambar" di tabel ticket.
     *   1. mgr.sv_attachment per report_no (ticket baru; tabel ini belum ada di sebagian
     *      database -> dilewati tanpa error)
     *   2. kalau tidak ada: gambar di ticket TWP (MySQL sv_entry_multi.picture) per complain_no
     * Hanya file yang benar-benar ada di folder gambar ticket (URL dari luar diabaikan);
     * URL dibuat ulang dengan alamat situs yang sedang dibuka.
     */
    public static function withPictures($rows)
    {
        $rows = collect($rows);
        if ($rows->isEmpty()) {
            return $rows;
        }
        $key = function ($entity, $project, $no) {
            return trim((string) $entity) . '|' . trim((string) $project) . '|' . trim((string) $no);
        };

        $byReport = [];
        try {
            $db = DB::connection('dblive');
            if ($db->select("SELECT OBJECT_ID('mgr.sv_attachment', 'U') AS id")[0]->id) {
                foreach ($rows->pluck('report_no')->filter()->map('trim')->unique()->chunk(500) as $chunk) {
                    $attachments = $db->table('mgr.sv_attachment')
                        ->whereIn('report_no', $chunk->values()->all())
                        ->orderBy('rowID')
                        ->get(['entity_cd', 'project_no', 'report_no', 'file_attachment', 'file_url']);
                    foreach ($attachments as $a) {
                        $k = $key($a->entity_cd, $a->project_no, $a->report_no);
                        $byReport[$k] = $byReport[$k] ?? (self::pictureUrl($a->file_url, true) ?: self::pictureUrl($a->file_attachment));
                    }
                }
            }
        } catch (\Throwable $e) {
            $byReport = [];
        }

        $byComplain = [];
        $complainNos = $rows->pluck('complain_no')->filter()->map('trim')->unique();
        foreach ($complainNos->chunk(500) as $chunk) {
            $tickets = DB::table('sv_entry_multi')
                ->whereIn('complain_no', $chunk->values()->all())
                ->whereNotNull('picture')->where('picture', '<>', '')
                ->get(['entity_cd', 'project_no', 'complain_no', 'picture']);
            foreach ($tickets as $t) {
                $byComplain[$key($t->entity_cd, $t->project_no, $t->complain_no)] = self::pictureUrl($t->picture);
            }
        }

        foreach ($rows as $row) {
            $row->picture_url = ($byReport[$key($row->entity_cd, $row->project_no, $row->report_no)] ?? null)
                ?: ($row->complain_no ? ($byComplain[$key($row->entity_cd, $row->project_no, $row->complain_no)] ?? null) : null);
        }

        return $rows;
    }

    /**
     * URL gambar ticket yang bisa dibuka: file di folder gambar ticket (lihat
     * Tenant\TicketController::TICKET_FILE_DIR) yang ada di disk -> url() situs ini.
     * $allowExternal: URL http(s) lain dipakai apa adanya (lampiran dari IFCA desktop).
     */
    private static function pictureUrl($value, $allowExternal = false)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        $dir = \App\Http\Controllers\Tenant\TicketController::TICKET_FILE_DIR;
        $path = rawurldecode((string) (parse_url($value, PHP_URL_PATH) ?: $value));
        $file = basename($path);
        $local = strpos($path, '/' . $dir . '/') !== false || strpos($value, '/') === false;

        if ($local && $file !== '' && is_file(base_path($dir . '/' . $file))) {
            return url($dir . '/' . rawurlencode($file));
        }
        return ($allowExternal && !$local && preg_match('#^https?://#i', $value)) ? $value : null;
    }

    /** Tanggal filter dari form (dd/mm/yyyy atau yyyy-mm-dd) -> 'Ymd'; null kalau kosong / tidak valid. */
    public static function toYmd($value)
    {
        return DateInput::format($value, 'Ymd');
    }

    /**
     * Batasi ke rentang tanggal lapor (termasuk tanggal akhir). $start / $end: 'Ymd'
     * (format yang selalu dibaca benar oleh SQL Server, tidak terpengaruh DATEFORMAT).
     */
    public static function between($query, $start, $end)
    {
        return $query
            ->where('t.reported_date', '>=', $start)
            ->where('t.reported_date', '<', date('Ymd', strtotime($end . ' +1 day')));
    }
}
