<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Support\TenantScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use DataTables;
use PDF;

/**
 * Permit Letter portal tenant: Work Permit (W), Entry (I) / Exit (O) Permit of Goods.
 *
 * Semua data disimpan di SQL Server (koneksi dblive):
 *   mgr.sv_entry_letter      header umum (complain_type = W/I/O, complain_no = nomor permit)
 *   mgr.permit_letter_hd/dtl detail Work Permit + daftar pekerja
 *   mgr.permit_goods_hd/dtl  detail Permit of Goods + daftar barang
 *   mgr.sv_entry_letter_log  log
 *   mgr.sv_spec.letter_no    nomor permit berikutnya per entity/project (LP100001, LP100002, ...)
 */
class PermitController extends Controller
{
    /** Jenis permit (complain_type) -> label. */
    public const TYPES = [
        'W' => 'Work Permit',
        'I' => 'Entry Permit of Goods',
        'O' => 'Exit Permit of Goods',
    ];

    /** Singkatan jenis permit untuk kop PDF. */
    public const SHORT = [
        'W' => 'WP',
        'I' => 'EPG',
        'O' => 'XPG',
    ];

    /** Status sv_entry_letter -> label (kode yang sama dengan modul ticket). */
    public const STATUSES = [
        'R' => 'Open',
        'A' => 'Accepted',
        'S' => 'Survey',
        'P' => 'Process',
        'F' => 'Confirm',
        'M' => 'Modify',
        'Z' => 'Charged Approved',
        'Y' => 'Approved',
        'C' => 'Closed',
        'X' => 'Cancel',
    ];

    private const AUDIT_USER = 'TWP';

    /** Form Request Permit. */
    public function index()
    {
        $tenancies = TenantScope::tenancies();
        $first = $tenancies->first();

        return view('tenant.permit.index', [
            'Tuname'     => Session::get('Tuname'),
            'datatenant' => $this->dataTenant(),
            'tenancies'  => $tenancies,
            'letter_no'  => $first ? $this->currentLetterNo($first->entity_cd, $first->project_no) : '',
            'types'      => self::TYPES,
        ]);
    }

    /**
     * Nomor permit berikutnya untuk tenancy yang dipilih di form (AJAX, JSON).
     * Dipakai saat combo tenant berubah: entity/project tiap tenancy bisa berbeda.
     */
    public function letterNo($id_tenancy)
    {
        $tenancy = $this->tenancyInScope($id_tenancy);

        return response()->json([
            'letter_no' => $tenancy ? $this->currentLetterNo($tenancy->entity_cd, $tenancy->project_no) : '',
        ]);
    }

    /**
     * Simpan permit (semua jenis). Dipanggil via AJAX, balasan JSON
     * {status: OK|Fail, pesan, permit_no?, errors?}.
     */
    public function save(Request $request)
    {
        $type = $request->input('permit_type');

        $rules = [
            'permit_type' => ['required', 'in:' . implode(',', array_keys(self::TYPES))],
            'tenant_no'   => ['required', 'integer'],
            'lot_no'      => ['required', 'string', 'max:8'],
            'floor'       => ['required', 'string', 'max:5'],
            'note'        => ['required', 'string', 'max:500'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
        ];

        if ($type === 'W') {
            $rules += [
                'incharge'      => ['required', 'string', 'max:50'],
                'contractor'    => ['required', 'string', 'max:50'],
                'job_type'      => ['required', 'string', 'max:50'],
                'work_tool'     => ['required', 'string', 'max:50'],
                'start_time'    => ['required', 'date_format:H:i,H:i:s'],
                'end_time'      => ['required', 'date_format:H:i,H:i:s', 'after:start_time'],
                'worker_name'   => ['required', 'array', 'min:1'],
                'worker_name.*' => ['required', 'string', 'max:50'],
            ];
        } else {
            $rules += [
                'company'     => ['required', 'string', 'max:50'],
                'owner'       => ['required', 'string', 'max:50'],
                'vehicle_no'  => ['required', 'string', 'max:10'],
                'item_name'   => ['required', 'array', 'min:1'],
                'item_name.*' => ['required', 'string', 'max:50'],
            ];
        }

        $validator = Validator::make($request->all(), $rules, [], [
            'tenant_no'     => 'tenant',
            'lot_no'        => 'unit',
            'incharge'      => 'person in charge',
            'contractor'    => 'contractor name',
            'work_tool'     => 'work tools',
            'worker_name'   => 'worker',
            'worker_name.*' => 'worker name',
            'company'       => 'company name',
            'owner'         => 'owner name',
            'vehicle_no'    => 'vehicle number',
            'item_name'     => 'item',
            'item_name.*'   => 'item name',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first(), 422, $validator->errors()->toArray());
        }

        try {
            $ctx = $this->permitContext($request->tenant_no, $request->lot_no);
            if (is_string($ctx)) {
                return $this->fail($ctx, 422);
            }

            $doc_no = DB::connection('dblive')->transaction(function () use ($ctx, $request, $type) {
                // Nomor permit diambil di dalam transaksi dengan lock baris sv_spec,
                // jadi dua request bersamaan tidak pernah mendapat nomor yang sama.
                $ctx['doc_no'] = $this->takeLetterNo($ctx['entity_cd'], $ctx['project_no']);

                $rows = $type === 'W'
                    ? $this->workPermitRows($ctx, $request)
                    : $this->goodsPermitRows($ctx, $request, $type);

                $db = DB::connection('dblive');
                $db->table('mgr.sv_entry_letter')->insert($rows['header']);
                $db->table($rows['detail_table'])->insert($rows['detail']);
                $db->table($rows['lines_table'])->insert($rows['lines']);
                $db->table('mgr.sv_entry_letter_log')->insert([
                    'entity_cd'   => $ctx['entity_cd'],
                    'project_no'  => $ctx['project_no'],
                    'complain_no' => $ctx['doc_no'],
                    'remarks'     => 'Request Created',
                    'audit_user'  => self::AUDIT_USER,
                    'audit_date'  => $ctx['audit_date'],
                ]);

                return $ctx['doc_no'];
            });

            return response()->json([
                'status'    => 'OK',
                'pesan'     => self::TYPES[$type] . ' ' . $doc_no . ' submitted successfully.',
                'permit_no' => $doc_no,
            ]);
        } catch (\Throwable $e) {
            Log::error('PermitController::save: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Detail error (pesan SQL, dsb.) hanya ditampilkan saat APP_DEBUG.
            return $this->fail(
                config('app.debug') ? 'Terjadi kesalahan: ' . $e->getMessage() : 'Failed to save permit, please try again.',
                500
            );
        }
    }

    /** Baris-baris Work Permit: header sv_entry_letter, permit_letter_hd, permit_letter_dtl. */
    private function workPermitRows(array $ctx, Request $request)
    {
        $start_date = $this->fmtDate($request->start_date);
        $end_date   = $this->fmtDate($request->end_date);
        $start_time = substr($request->start_time, 0, 5);
        $end_time   = substr($request->end_time, 0, 5);

        $header = $this->headerRow($ctx, 'W', $request) + [
            'pj_name'    => $request->incharge,
            'contractor' => $request->contractor,
            'job_type'   => $request->job_type,
            'work_tool'  => $request->work_tool,
            'start_time' => $start_time,
            'end_time'   => $end_time,
        ];

        $detail = [
            'entity_cd'       => $ctx['entity_cd'],
            'project_no'      => $ctx['project_no'],
            'doc_no'          => $ctx['doc_no'],
            'member_email'    => $ctx['member_email'],
            'member_name'     => $ctx['member_name'],
            'member_hp'       => $ctx['member_hp'],
            'debtor_acct'     => $ctx['debtor_acct'],
            'pic_name'        => $request->incharge,
            'kontraktor_name' => $request->contractor,
            'tower'           => $ctx['tower'],
            'floor'           => $request->floor,
            'unit'            => $ctx['lot_no'],
            'work_type'       => $request->job_type,
            'work_tools'      => $request->work_tool,
            'start_day'       => date('l', strtotime($request->start_date)),
            'end_day'         => date('l', strtotime($request->end_date)),
            'start_date'      => $start_date,
            'end_date'        => $end_date,
            'start_time'      => $start_time,
            'end_time'        => $end_time,
            'note'            => $request->note,
            'audit_user'      => self::AUDIT_USER,
            'audit_date'      => $ctx['audit_date'],
        ];

        $lines = [];
        foreach ($request->worker_name as $name) {
            $lines[] = $this->lineRow($ctx) + ['staff_name' => trim($name)];
        }

        return [
            'header'       => $header,
            'detail_table' => 'mgr.permit_letter_hd',
            'detail'       => $detail,
            'lines_table'  => 'mgr.permit_letter_dtl',
            'lines'        => $lines,
        ];
    }

    /** Baris-baris Entry/Exit Permit of Goods: header sv_entry_letter, permit_goods_hd, permit_goods_dtl. */
    private function goodsPermitRows(array $ctx, Request $request, $type)
    {
        $header = $this->headerRow($ctx, $type, $request) + [
            'company_name' => $request->company,
            'owner_name'   => $request->owner,
            'vehicle_no'   => $request->vehicle_no,
        ];

        $detail = [
            'entity_cd'    => $ctx['entity_cd'],
            'project_no'   => $ctx['project_no'],
            'doc_no'       => $ctx['doc_no'],
            'member_email' => $ctx['member_email'],
            'member_name'  => $ctx['member_name'],
            'member_hp'    => $ctx['member_hp'],
            'debtor_acct'  => $ctx['debtor_acct'],
            'company_name' => $request->company,
            'owner_name'   => $request->owner,
            'tower'        => $ctx['tower'],
            'floor'        => $request->floor,
            'unit'         => $ctx['lot_no'],
            'start_date'   => $this->fmtDate($request->start_date),
            'vehicle_no'   => $request->vehicle_no,
            'work_type'    => self::TYPES[$type],
            'note'         => $request->note,
            'audit_user'   => self::AUDIT_USER,
            'audit_date'   => $ctx['audit_date'],
        ];

        // Form hanya punya nama barang; item_descs NOT NULL, diisi sama.
        $lines = [];
        foreach ($request->item_name as $name) {
            $lines[] = $this->lineRow($ctx) + ['item_name' => trim($name), 'item_descs' => trim($name)];
        }

        return [
            'header'       => $header,
            'detail_table' => 'mgr.permit_goods_hd',
            'detail'       => $detail,
            'lines_table'  => 'mgr.permit_goods_dtl',
            'lines'        => $lines,
        ];
    }

    /** Kolom sv_entry_letter yang sama untuk semua jenis permit. */
    private function headerRow(array $ctx, $type, Request $request)
    {
        return [
            'entity_cd'       => $ctx['entity_cd'],
            'project_no'      => $ctx['project_no'],
            'debtor_acct'     => $ctx['debtor_acct'],
            'complain_type'   => $type,
            'complain_no'     => $ctx['doc_no'],
            'reported_by'     => self::AUDIT_USER,
            'reported_date'   => $ctx['audit_date'],
            'floor'           => $request->floor,
            'serv_req_by'     => $ctx['member_name'],
            'contact_no'      => $ctx['member_hp'],
            'billing_type'    => 'T',
            'status'          => 'R',
            'audit_user'      => self::AUDIT_USER,
            'audit_date'      => $ctx['audit_date'],
            'complain_source' => 'LETTER',
            'lot_no'          => $ctx['lot_no'],
            'post_status'     => 'N',
            'note'            => $request->note,
            'start_date'      => $this->fmtDate($request->start_date),
            'end_date'        => $this->fmtDate($request->end_date),
        ];
    }

    /** Kolom kunci baris detail pekerja / barang. */
    private function lineRow(array $ctx)
    {
        return [
            'entity_cd'   => $ctx['entity_cd'],
            'project_no'  => $ctx['project_no'],
            'doc_no'      => $ctx['doc_no'],
            'debtor_acct' => $ctx['debtor_acct'],
            'lot_no'      => $ctx['lot_no'],
            'audit_user'  => self::AUDIT_USER,
            'audit_date'  => $ctx['audit_date'],
        ];
    }

    /**
     * Data bersama untuk semua jenis permit: tenancy yang dipilih (dicek cakupannya),
     * tower dari unit, dan data pemohon. Nomor dokumen diambil belakangan di dalam transaksi.
     * Mengembalikan array, atau string pesan error kalau ada yang tidak valid.
     */
    private function permitContext($id_tenancy, $lot_no)
    {
        $tenant = $this->dataTenant();
        if (!$tenant) {
            return 'Tenant data not found.';
        }

        $tenancy = $this->tenancyInScope($id_tenancy);
        if (!$tenancy) {
            return 'The selected tenant is not valid.';
        }

        // Tower (block_no) dari unit yang dipilih
        $lot = DB::connection('dblive')->table('mgr.pm_lot')
            ->where('entity_cd', $tenancy->entity_cd)
            ->where('project_no', $tenancy->project_no)
            ->where('lot_no', $lot_no)
            ->select('block_no')
            ->first();

        if (!$lot) {
            return 'Sorry, the tower with this lot number is not found.';
        }

        return [
            'entity_cd'    => $tenancy->entity_cd,
            'project_no'   => $tenancy->project_no,
            'debtor_acct'  => $tenancy->tenant_no,
            'lot_no'       => $lot_no,
            'tower'        => $lot->block_no,
            // Panjang kolom di SQL Server: member_name varchar(50), member_hp/contact_no varchar(20)
            'member_name'  => mb_substr((string) Session::get('Tuname'), 0, 50),
            'member_email' => mb_substr((string) Session::get('Tenemail'), 0, 60),
            'member_hp'    => mb_substr((string) $tenant->handphone, 0, 20),
            'audit_date'   => now()->format('Y-m-d\TH:i:s'),   // ISO 8601 dengan 'T': tidak terpengaruh DATEFORMAT
        ];
    }

    /** Nomor permit yang akan dipakai berikutnya (hanya untuk ditampilkan di form). */
    private function currentLetterNo($entity_cd, $project_no)
    {
        return (string) DB::connection('dblive')->table('mgr.sv_spec')
            ->where('entity_cd', $entity_cd)
            ->where('project_no', $project_no)
            ->value('letter_no');
    }

    /**
     * Ambil nomor permit dan naikkan sv_spec.letter_no (LP100001 -> LP100002) secara atomik.
     * Harus dipanggil di dalam transaksi: baris sv_spec dikunci (updlock/holdlock) sampai commit,
     * sehingga request lain menunggu dan mendapat nomor berikutnya, bukan nomor yang sama.
     */
    private function takeLetterNo($entity_cd, $project_no)
    {
        $db = DB::connection('dblive');

        $spec = $db->table('mgr.sv_spec')
            ->where('entity_cd', $entity_cd)
            ->where('project_no', $project_no)
            ->lockForUpdate()
            ->first();

        $doc_no = trim((string) ($spec->letter_no ?? ''));
        if ($doc_no === '') {
            throw new \RuntimeException('Permit number (sv_spec.letter_no) not found for ' . trim($entity_cd) . ' / ' . trim($project_no));
        }

        $updated = $db->table('mgr.sv_spec')
            ->where('entity_cd', $entity_cd)
            ->where('project_no', $project_no)
            ->where('letter_no', $doc_no)
            ->update(['letter_no' => $this->nextLetterNo($doc_no)]);

        if ($updated !== 1) {
            throw new \RuntimeException('Permit number ' . $doc_no . ' has already been used, please submit again.');
        }

        return $doc_no;
    }

    /** LP100001 -> LP100002 (prefix & jumlah digit dipertahankan). */
    private function nextLetterNo($letter_no)
    {
        if (!preg_match('/^(.*?)(\d+)$/', $letter_no, $m)) {
            throw new \RuntimeException('Invalid permit number format: ' . $letter_no);
        }

        return $m[1] . str_pad((int) $m[2] + 1, strlen($m[2]), '0', STR_PAD_LEFT);
    }

    /**
     * Baris pm_tenancy yang dipilih di combo (value = pm_tenancy.id), hanya kalau masuk
     * cakupan TenantScope (tenant biasa: miliknya sendiri; admin: semua tenancy aktif).
     */
    private function tenancyInScope($id_tenancy)
    {
        $tenancy = DB::table('pm_tenancy')->where('id', (int) $id_tenancy)->first();

        if (!$tenancy || !in_array((string) $tenancy->tenant_no, TenantScope::tenantNos(), true)) {
            return null;
        }

        return $tenancy;
    }

    /** Data tenant yang sedang login (contact, handphone, tenant_no, business_no). */
    private function dataTenant()
    {
        return DB::table('tenant as t')
            ->leftJoin('all_login as al', 't.email', '=', 'al.email')
            ->leftJoin('pm_tenancy as pt', 't.tenant_no_df', '=', 'pt.tenant_no')
            ->where('al.email', Session::get('Tenemail'))
            // satu email bisa punya baris admin dan tenant di all_login; utamakan baris tenant
            ->orderByRaw("CASE WHEN al.tableforeign = 'tenant' THEN 0 ELSE 1 END")
            ->select(
                't.contact_name as contact_name',
                't.flag as flag',
                'al.handphone as handphone',
                'pt.tenant_no as tenant_no',
                'pt.business_no as business_no'
            )
            ->first();
    }

    /**
     * Tanggal untuk kolom datetime SQL Server, format yyyymmdd.
     * Untuk tipe datetime, 'dd/mm/yyyy', 'yyyy-mm-dd' dan 'yyyy-mm-dd hh:mm:ss' semuanya
     * ikut SET DATEFORMAT sesi (koneksi ODBC ini dmy -> '2026-09-21' dibaca y-d-m, bulan 21).
     * Hanya 'yyyymmdd' dan 'yyyy-mm-ddThh:mm:ss' yang selalu dibaca benar.
     */
    private function fmtDate($date)
    {
        return $date ? date('Ymd', strtotime($date)) : null;
    }

    private function fail($pesan, $code = 500, array $errors = [])
    {
        $body = ['status' => 'Fail', 'pesan' => $pesan];
        if ($errors) {
            $body['errors'] = $errors;
        }

        return response()->json($body, $code);
    }

    /** Halaman History Permit (isi tabel diambil via table()). */
    public function history()
    {
        return view('tenant.permit.history', [
            'types'    => self::TYPES,
            'statuses' => self::STATUSES,
        ]);
    }

    /** Sumber data DataTables (server side) untuk History Permit. */
    public function table(Request $request)
    {
        // Detail permit ada di dua tabel: permit_letter_hd (Work Permit) dan
        // permit_goods_hd (Entry/Exit Permit of Goods), jadi keduanya ikut di-join
        // dan tower/floor/unit diambil dari mana pun yang terisi.
        $permit = DB::connection('dblive')
            ->table('mgr.sv_entry_letter as sel')
            ->leftJoin('mgr.permit_letter_hd as plh', function ($join) {
                $join->on('sel.entity_cd', '=', 'plh.entity_cd')
                    ->on('sel.project_no', '=', 'plh.project_no')
                    ->on('sel.complain_no', '=', 'plh.doc_no');
            })
            ->leftJoin('mgr.permit_goods_hd as pgh', function ($join) {
                $join->on('sel.entity_cd', '=', 'pgh.entity_cd')
                    ->on('sel.project_no', '=', 'pgh.project_no')
                    ->on('sel.complain_no', '=', 'pgh.doc_no');
            })
            ->whereIn('sel.debtor_acct', TenantScope::tenantNos())
            ->whereIn('sel.complain_type', array_keys(self::TYPES))
            ->select(
                'sel.complain_no as complain_no',
                'sel.complain_type as complain_type',
                'sel.note as note',
                'sel.start_time as start_time',
                'sel.end_time as end_time',
                'sel.start_date as start_date',
                'sel.end_date as end_date',
                'sel.status as status',
                'sel.audit_date as audit_date',
                DB::raw('COALESCE(plh.tower, pgh.tower) as tower'),
                DB::raw('COALESCE(plh.floor, pgh.floor, sel.floor) as floor'),
                DB::raw('COALESCE(plh.unit, pgh.unit, sel.lot_no) as unit')
            );

        // Filter opsional, boleh dipakai sendiri-sendiri atau digabung. Kalau semuanya kosong
        // (kondisi saat halaman pertama dibuka) seluruh permit milik tenant ikut tampil.
        $permit_no   = trim((string) $request->permit_no);
        $permit_type = strtoupper(trim((string) $request->permit_type));
        $status      = strtoupper(trim((string) $request->status));
        $start_date  = $this->parseDmy($request->start_date);

        if ($permit_no !== '') {
            $permit->where('sel.complain_no', 'like', '%' . $permit_no . '%');
        }

        if (isset(self::TYPES[$permit_type])) {
            $permit->where('sel.complain_type', $permit_type);
        }

        if (isset(self::STATUSES[$status])) {
            $permit->where('sel.status', $status);
        }

        if ($start_date) {
            // Kolom datetime, jadi diambil satu hari penuh.
            $permit->where('sel.start_date', '>=', $this->fmtDate($start_date))
                ->where('sel.start_date', '<', $this->fmtDate($start_date . ' +1 day'));
        }

        // Dibungkus jadi subquery supaya kolom hasil join (note, floor, start_date, ...)
        // tidak ambigu saat DataTables melakukan search / order per kolom.
        // Tanpa order di sini: SQL Server menolak ORDER BY di derived table, dan query
        // count milik DataTables membungkus query ini lagi. Urutan diterapkan lewat
        // callback order() di bawah.
        $query = DB::connection('dblive')
            ->query()
            ->fromSub($permit, 'p');

        return DataTables::of($query)
            ->addIndexColumn()
            ->order(function ($query) use ($request) {
                // Kalau user klik header kolom, ikuti urutan itu. Kalau tidak
                // (halaman baru dibuka), urutkan dari tanggal input permit terbaru.
                $orders = (array) $request->input('order', []);
                if (empty($orders)) {
                    $query->orderBy('audit_date', 'desc');
                    return;
                }

                foreach ($orders as $order) {
                    $column = $request->input('columns.' . ($order['column'] ?? '') . '.data');
                    if ($column && $column !== 'DT_RowIndex') {
                        $query->orderBy($column, strtolower($order['dir'] ?? '') === 'asc' ? 'asc' : 'desc');
                    }
                }
            })
            ->make(true);
    }

    /** 'dd/mm/yyyy' -> 'yyyy-mm-dd'; null kalau kosong atau format lain. */
    private function parseDmy($date)
    {
        if (!$date || !preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', trim($date), $m)) {
            return null;
        }

        return $m[3] . '-' . $m[2] . '-' . $m[1];
    }

    /**
     * Cetak satu permit dari halaman History ke PDF (tombol Print di tabel).
     * Hanya permit milik tenant yang sedang login (TenantScope) yang bisa dibuka.
     */
    public function printPdf($doc_no)
    {
        $db = DB::connection('dblive');

        $header = $db->table('mgr.sv_entry_letter')
            ->where('complain_no', $doc_no)
            ->whereIn('debtor_acct', TenantScope::tenantNos())
            ->whereIn('complain_type', array_keys(self::TYPES))
            ->first();

        if (!$header) {
            abort(404, 'Permit not found.');
        }

        $keys = [
            'entity_cd'  => $header->entity_cd,
            'project_no' => $header->project_no,
            'doc_no'     => $header->complain_no,
        ];

        if ($header->complain_type === 'W') {
            $detail = $db->table('mgr.permit_letter_hd')->where($keys)->first();
            $lines = $db->table('mgr.permit_letter_dtl')->where($keys)->orderBy('rowID')->pluck('staff_name')->all();
            $lines_title = 'Worker Name';
        } else {
            $detail = $db->table('mgr.permit_goods_hd')->where($keys)->first();
            $lines = $db->table('mgr.permit_goods_dtl')->where($keys)->orderBy('rowID')->pluck('item_name')->all();
            $lines_title = 'Item Name';
        }

        // Nama pengelola gedung & project untuk kop surat.
        $tenancy = DB::table('pm_tenancy')
            ->where('tenant_no', $header->debtor_acct)
            ->where('entity_cd', trim($header->entity_cd))
            ->where('project_no', trim($header->project_no))
            ->first();

        $tenant = DB::table('tenant')->where('tenant_no_df', $header->debtor_acct)->first();

        $status = trim((string) $header->status);

        return PDF::loadView('tenant.permit.print', [
            'header'       => $header,
            'detail'       => $detail,
            'lines'        => $lines,
            'lines_title'  => $lines_title,
            'tenancy'      => $tenancy,
            'tenant'       => $tenant,
            'title'        => strtoupper(self::TYPES[$header->complain_type]),
            'short'        => self::SHORT[$header->complain_type],
            'type'         => $header->complain_type,
            'types'        => self::TYPES,
            'status_label' => self::STATUSES[$status] ?? ($status !== '' ? $status : '-'),
        ])
            ->setPaper('a4', 'portrait')
            ->stream($header->complain_no . '.pdf');
    }
}
