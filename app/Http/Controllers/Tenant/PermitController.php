<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Support\TenantScope;
use DataTables;
use PDF;

class PermitController extends Controller
{
    public function index()
    {
        $Tuname = Session::get('Tuname');
        $Tenemail = Session::get('Tenemail');
        $entity_cd = Session::get('entity_cd');
        $project_no = Session::get('project_no');
        $buss_id = Session::get('business_no');
        $tenant_no = Session::get('tenant_df');

        $datatenant = $this->dataTenant();
        
        $crit = array(
            'business_no' => $buss_id,
            'tenant_no'   => $tenant_no
        );

        $data_tenancy = DB::table('pm_tenancy')->where($crit)->get();
        $combo_tenant='';
        if($data_tenancy){
            $combo_tenant = $this->get_combo($buss_id, $data_tenancy[0]->id, $project_no);
        }

        $where = array(
            'entity_cd' => $entity_cd,
            'project_no'   => $project_no
        );

        $no_ticket = DB::connection('dblive')
            ->table('mgr.sv_spec')
            ->where($where)
            ->first();

        $content = array(
            'entity_cd' => $entity_cd,
            'project_no' => $project_no,
            'Tuname' => $Tuname,
            'Tenemail' => $Tenemail,
            'datatenant' => $datatenant,
            'combo_tenant' => $combo_tenant,
            'letter_no' => $no_ticket->letter_no ?? ''
        );
        return view('tenant.permit.index', $content);
    }

    public function getTicketNew(Request $request)
    {
        $where = [
            'entity_cd'  => $request->ent,
            'project_no' => $request->prj,
        ];

        $data = DB::connection('dblive')
            ->table('mgr.sv_spec')
            ->where($where)
            ->first();

        if ($data) {
            return response()->json($data->letter_no);
        }

        return response()->json(1);
    }

    /**
     * Combo tenancy (tenant_no) di form ticket. Tenant biasa: tenancy milik business-nya;
     * mode semua tenant (admin): seluruh tenancy aktif.
     */
    function get_combo($business_no = "", $selected_id = "", $project_no = "")
    {
        if (TenantScope::all()) {
            $query = DB::table('pm_tenancy')->where('status', 'A')->orderBy('tenant_no')->get();
        } else {
            $where = array('business_no'=> $business_no,
                    'project_no'=>$project_no);
            $query = DB::table('pm_tenancy')->where($where)->get();
        }
        $combo[] = '<option></option>';
        $combo[] = "\n";
        foreach ($query as $result) {
            $value = TenantScope::all() ? $result->tenant_no.' - '.$result->entity_desc : $result->tenant_no;
            $combo[] = '<option value="' . $result->id . '" data-entity="'.$result->entity_cd.'" data-project="'.$result->project_no.'" '. '>' . $value . '</option>';
            $combo[] = "\n";
        }
        return implode("", $combo);
    }

    /**
     * Work Permit (permit_type = W).
     * Tujuan (SQL Server): mgr.sv_entry_letter (header) + mgr.permit_letter_hd + mgr.permit_letter_dtl (pekerja).
     * Dipanggil via AJAX, balasan JSON {status: OK|Fail, pesan}.
     */
    public function workpermit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'permit_type'   => ['required', 'in:W'],
                'tenant_no'     => ['required', 'integer'],
                'lot_no'        => ['required', 'max:8'],
                'incharge'      => ['required', 'max:50'],
                'contractor'    => ['required', 'max:50'],
                'floor'         => ['required', 'max:5'],
                'job_type'      => ['required', 'max:50'],
                'work_tool'     => ['required', 'max:50'],
                'note'          => ['required', 'max:500'],
                'start_date'    => ['required', 'date'],
                'end_date'      => ['required', 'date', 'after_or_equal:start_date'],
                'start_time'    => ['required', 'date_format:H:i,H:i:s'],
                'end_time'      => ['required', 'date_format:H:i,H:i:s', 'after:start_time'],
                'worker_name'   => ['required', 'array', 'min:1'],
                'worker_name.*' => ['required', 'string', 'max:50'],
            ]);

            if ($validator->fails()) {
                return $this->fail($validator->errors()->first(), 422);
            }

            $ctx = $this->permitContext($request->tenant_no, $request->lot_no);
            if (is_string($ctx)) {
                return $this->fail($ctx, 422);
            }

            $start_time = substr($request->start_time, 0, 5);
            $end_time   = substr($request->end_time, 0, 5);
            $start_date = $this->fmtDate($request->start_date);
            $end_date   = $this->fmtDate($request->end_date);

            // Header umum (sv_entry_letter)
            $dt_sv_entry_letter = array_merge($this->svEntryLetterBase($ctx, 'W', $request->floor), [
                'note'       => $request->note,
                'pj_name'    => $request->incharge,
                'contractor' => $request->contractor,
                'job_type'   => $request->job_type,
                'work_tool'  => $request->work_tool,
                'start_date'  => $start_date,   // nama kolom asli di sv_entry_letter
                'end_date'   => $end_date,
                'start_time' => $start_time,
                'end_time'   => $end_time,
            ]);

            // Detail work permit (permit_letter_hd)
            $dt_permit_letter_hd = [
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
                'audit_user'      => $ctx['audit_user'],
                'audit_date'      => $ctx['audit_date'],
            ];

            // Log (sv_entry_letter_log)
            $data_log = $this->svEntryLetterLog($ctx, 'Request Created');

            // Pekerja (permit_letter_dtl)
            $dt_permit_letter_dtl = [];
            foreach ($request->worker_name as $name) {
                $dt_permit_letter_dtl[] = [
                    'entity_cd'   => $ctx['entity_cd'],
                    'project_no'  => $ctx['project_no'],
                    'doc_no'      => $ctx['doc_no'],
                    'debtor_acct' => $ctx['debtor_acct'],
                    'lot_no'      => $ctx['lot_no'],
                    'staff_name'  => trim($name),
                    'audit_user'  => $ctx['audit_user'],
                    'audit_date'  => $ctx['audit_date'],
                ];
            }

            DB::connection('dblive')->transaction(function () use ($ctx, $dt_sv_entry_letter, $dt_permit_letter_hd, $dt_permit_letter_dtl, $data_log) {
                DB::connection('dblive')->table('mgr.sv_entry_letter')->insert($dt_sv_entry_letter);
                DB::connection('dblive')->table('mgr.permit_letter_hd')->insert($dt_permit_letter_hd);
                DB::connection('dblive')->table('mgr.permit_letter_dtl')->insert($dt_permit_letter_dtl);
                DB::connection('dblive')->table('mgr.sv_entry_letter_log')->insert($data_log);
                $this->incrementLetterNo($ctx);
            });

            return response()->json([
                'status'    => 'OK',
                'pesan'     => 'Work Permit ' . $ctx['doc_no'] . ' submitted successfully.',
                'permit_no' => $ctx['doc_no'],
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error in workpermit(): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->fail('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Entry (I) / Exit (O) Permit of Goods.
     * Tujuan (SQL Server): mgr.sv_entry_letter (header) + mgr.permit_goods_hd + mgr.permit_goods_dtl (barang).
     * Dipanggil via AJAX, balasan JSON {status: OK|Fail, pesan}.
     */
    public function permitofgoods(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'permit_type'       => ['required', 'in:I,O'],
                'permit_tenant_no'  => ['required', 'integer'],
                'permit_lot_no'     => ['required', 'max:8'],
                'company'           => ['required', 'max:50'],
                'owner'             => ['required', 'max:50'],
                'permit_floor'      => ['required', 'max:5'],
                'vehicle_no'        => ['required', 'max:10'],
                'notes'             => ['required', 'max:500'],
                'permit_start_date' => ['required', 'date'],
                'permit_end_date'   => ['required', 'date', 'after_or_equal:permit_start_date'],
                'item_name'         => ['required', 'array', 'min:1'],
                'item_name.*'       => ['required', 'string', 'max:50'],
            ]);

            if ($validator->fails()) {
                return $this->fail($validator->errors()->first(), 422);
            }

            $ctx = $this->permitContext($request->permit_tenant_no, $request->permit_lot_no);
            if (is_string($ctx)) {
                return $this->fail($ctx, 422);
            }

            $permit_type = $request->permit_type;
            $label = $permit_type === 'I' ? 'Entry Permit of Goods' : 'Exit Permit of Goods';
            $start_date  = $this->fmtDate($request->permit_start_date);
            $end_date    = $this->fmtDate($request->permit_end_date);

            // Header umum (sv_entry_letter)
            $dt_sv_entry_letter = array_merge($this->svEntryLetterBase($ctx, $permit_type, $request->permit_floor), [
                'note'         => $request->notes,
                'company_name' => $request->company,
                'owner_name'   => $request->owner,
                'vehicle_no'   => $request->vehicle_no,
                'start_date'    => $start_date,
                'end_date'     => $end_date,
            ]);

            // Detail permit of goods (permit_goods_hd)
            $dt_permit_goods_hd = [
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
                'floor'        => $request->permit_floor,
                'unit'         => $ctx['lot_no'],
                'start_date'   => $start_date,
                'vehicle_no'   => $request->vehicle_no,
                'work_type'    => $label,
                'note'         => $request->notes,
                'audit_user'   => $ctx['audit_user'],
                'audit_date'   => $ctx['audit_date'],
            ];

            // Barang (permit_goods_dtl) - form hanya punya nama; item_descs NOT NULL, diisi sama
            $dt_permit_goods_dtl = [];
            foreach ($request->item_name as $name) {
                $dt_permit_goods_dtl[] = [
                    'entity_cd'   => $ctx['entity_cd'],
                    'project_no'  => $ctx['project_no'],
                    'doc_no'      => $ctx['doc_no'],
                    'debtor_acct' => $ctx['debtor_acct'],
                    'lot_no'      => $ctx['lot_no'],
                    'item_name'   => trim($name),
                    'item_descs'  => trim($name),
                    'audit_user'  => $ctx['audit_user'],
                    'audit_date'  => $ctx['audit_date'],
                ];
            }

            // Log (sv_entry_letter_log)
            $data_log = $this->svEntryLetterLog($ctx, 'Request Created');

            DB::connection('dblive')->transaction(function () use ($ctx, $dt_sv_entry_letter, $dt_permit_goods_hd, $dt_permit_goods_dtl, $data_log) {
                DB::connection('dblive')->table('mgr.sv_entry_letter')->insert($dt_sv_entry_letter);
                DB::connection('dblive')->table('mgr.permit_goods_hd')->insert($dt_permit_goods_hd);
                DB::connection('dblive')->table('mgr.permit_goods_dtl')->insert($dt_permit_goods_dtl);
                DB::connection('dblive')->table('mgr.sv_entry_letter_log')->insert($data_log);
                $this->incrementLetterNo($ctx);
            });

            return response()->json([
                'status'    => 'OK',
                'pesan'     => $label . ' ' . $ctx['doc_no'] . ' submitted successfully.',
                'permit_no' => $ctx['doc_no'],
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error in permitofgoods(): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->fail('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Data bersama untuk semua jenis permit: tenancy yang dipilih (dicek cakupannya),
     * tower dari unit, nomor dokumen (letter_no di sv_spec), dan data pemohon.
     * Mengembalikan array, atau string pesan error kalau ada yang tidak valid.
     */
    private function permitContext($id_tenancy, $lot_no)
    {
        $tenant = $this->dataTenant();
        if (!$tenant) {
            return 'Data tenant tidak ditemukan.';
        }

        $tenancy = $this->tenancyInScope($id_tenancy);
        if (!$tenancy) {
            return 'Tenant yang dipilih tidak valid.';
        }

        $crit = ['entity_cd' => $tenancy->entity_cd, 'project_no' => $tenancy->project_no];

        // Tower (block_no) dari unit yang dipilih
        $lot = DB::connection('dblive')->table('mgr.pm_lot')
            ->where($crit)
            ->where('lot_no', $lot_no)
            ->select('block_no')
            ->first();

        if (!$lot) {
            return 'Sorry, the tower with this lot number is not found';
        }

        // Nomor dokumen = letter_no di sv_spec (yang tampil di form sebagai Permit Number)
        $spec = DB::connection('dblive')->table('mgr.sv_spec')->where($crit)->first();
        $doc_no = $spec->letter_no ?? null;

        if (!$doc_no) {
            return 'Permit number (sv_spec.letter_no) not found for ' . $tenancy->entity_cd . ' / ' . $tenancy->project_no;
        }

        return [
            'entity_cd'    => $tenancy->entity_cd,
            'project_no'   => $tenancy->project_no,
            'debtor_acct'  => $tenancy->tenant_no,
            'doc_no'       => (string) $doc_no,
            'lot_no'       => $lot_no,
            'tower'        => $lot->block_no,
            'member_name'  => Session::get('Tuname'),
            'member_email' => Session::get('Tenemail'),
            'member_hp'    => $tenant->handphone,
            'audit_user'   => 'TWP',
            'audit_date'   => now()->format('Y-m-d\TH:i:s'),   // ISO 8601 dengan 'T': tidak terpengaruh DATEFORMAT
        ];
    }

    /**
     * Setelah permit tersimpan: naikkan sv_spec.letter_no (LP100001 -> LP100002).
     * WHERE ikut letter_no yang dipakai, jadi kalau nomor itu sudah diambil request lain
     * (0 baris ter-update) transaksi dibatalkan dan tidak ada nomor dobel.
     */
    private function incrementLetterNo(array $ctx)
    {
        $updated = DB::connection('dblive')->table('mgr.sv_spec')
            ->where('entity_cd', $ctx['entity_cd'])
            ->where('project_no', $ctx['project_no'])
            ->where('letter_no', $ctx['doc_no'])
            ->update(['letter_no' => $this->nextLetterNo($ctx['doc_no'])]);

        if ($updated !== 1) {
            throw new \Exception('Permit number ' . $ctx['doc_no'] . ' has already been used, please submit again.');
        }
    }

    /** LP100001 -> LP100002 (prefix & jumlah digit dipertahankan). */
    private function nextLetterNo($letter_no)
    {
        if (!preg_match('/^(.*?)(\d+)$/', $letter_no, $m)) {
            throw new \Exception('Invalid permit number format: ' . $letter_no);
        }

        return $m[1] . str_pad((int) $m[2] + 1, strlen($m[2]), '0', STR_PAD_LEFT);
    }

    /** Baris log sv_entry_letter_log untuk permit ini. */
    private function svEntryLetterLog(array $ctx, $remarks)
    {
        return [
            'entity_cd'   => $ctx['entity_cd'],
            'project_no'  => $ctx['project_no'],
            'complain_no' => $ctx['doc_no'],
            'remarks'     => $remarks,
            'audit_user'  => $ctx['audit_user'],
            'audit_date'  => $ctx['audit_date'],
        ];
    }

    /** Kolom sv_entry_letter yang sama untuk W / I / O. */
    private function svEntryLetterBase(array $ctx, $complain_type, $floor)
    {
        return [
            'entity_cd'       => $ctx['entity_cd'],
            'project_no'      => $ctx['project_no'],
            'debtor_acct'     => $ctx['debtor_acct'],
            'complain_type'   => $complain_type,
            'complain_no'     => $ctx['doc_no'],
            'reported_by'     => $ctx['audit_user'],
            'reported_date'   => $ctx['audit_date'],
            'floor'           => $floor,
            'serv_req_by'     => $ctx['member_name'],
            'contact_no'      => $ctx['member_hp'],
            'billing_type'    => 'T',
            'status'          => 'R',
            'audit_user'      => $ctx['audit_user'],
            'audit_date'      => $ctx['audit_date'],
            'complain_source' => 'LETTER',
            'lot_no'          => $ctx['lot_no'],
            'post_status'     => 'N',
        ];
    }

    /**
     * Baris pm_tenancy yang dipilih di combo (value = pm_tenancy.id), hanya kalau masuk
     * cakupan TenantScope (tenant biasa: miliknya sendiri; admin: semua tenancy aktif).
     */
    private function tenancyInScope($id_tenancy)
    {
        $tenancy = DB::table('pm_tenancy')->where('id', $id_tenancy)->first();

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

    private function fail($pesan, $code = 500)
    {
        return response()->json([
            'status' => 'Fail',
            'pesan'  => $pesan,
        ], $code);
    }

    /** Halaman History Permit (isi tabel diambil via permitTable). */
    public function HistoryPermit()
    {
        $content = array(
            'entity_cd'  => Session::get('entity_cd'),
            'project_no' => Session::get('project_no'),
            'Tuname'     => Session::get('Tuname'),
            'Tenemail'   => Session::get('Tenemail'),
            'datatenant' => $this->dataTenant(),
        );

        return view('tenant.permit.history', $content);
    }

    /** Sumber data DataTables (server side) untuk History Permit. */
    public function permitTable(Request $request)
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
            ->whereIn('sel.complain_type', ['W', 'I', 'O'])
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
        $start_date  = $this->parseDmy($request->start_date);

        if ($permit_no !== '') {
            $permit->where('sel.complain_no', 'like', '%' . $permit_no . '%');
        }

        if (in_array($permit_type, ['W', 'I', 'O'], true)) {
            $permit->where('sel.complain_type', $permit_type);
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
                    $column = $request->input('columns.' . $order['column'] . '.data');
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
    public function printPermit($doc_no)
    {
        $header = DB::connection('dblive')
            ->table('mgr.sv_entry_letter')
            ->where('complain_no', $doc_no)
            ->whereIn('debtor_acct', TenantScope::tenantNos())
            ->whereIn('complain_type', ['W', 'I', 'O'])
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
            $detail = DB::connection('dblive')->table('mgr.permit_letter_hd')->where($keys)->first();
            $lines = DB::connection('dblive')
                ->table('mgr.permit_letter_dtl')
                ->where($keys)
                ->orderBy('rowID')
                ->pluck('staff_name')
                ->all();
            $lines_title = 'Worker Name';
        } else {
            $detail = DB::connection('dblive')->table('mgr.permit_goods_hd')->where($keys)->first();
            $lines = DB::connection('dblive')
                ->table('mgr.permit_goods_dtl')
                ->where($keys)
                ->orderBy('rowID')
                ->pluck('item_name')
                ->all();
            $lines_title = 'Item Name';
        }

        // Nama pengelola gedung & project untuk kop surat.
        $tenancy = DB::table('pm_tenancy')
            ->where('tenant_no', $header->debtor_acct)
            ->where('entity_cd', $header->entity_cd)
            ->where('project_no', $header->project_no)
            ->first();

        $tenant = DB::table('tenant')->where('tenant_no_df', $header->debtor_acct)->first();

        $labels = [
            'W' => 'WORK PERMIT',
            'I' => 'ENTRY PERMIT OF GOODS',
            'O' => 'EXIT PERMIT OF GOODS',
        ];

        $content = [
            'header'      => $header,
            'detail'      => $detail,
            'lines'       => $lines,
            'lines_title' => $lines_title,
            'tenancy'     => $tenancy,
            'tenant'      => $tenant,
            'title'       => $labels[$header->complain_type],
            'short'       => $header->complain_type === 'W' ? 'WP' : ($header->complain_type === 'I' ? 'EPG' : 'XPG'),
        ];

        return PDF::loadView('tenant.permit.print', $content)
            ->setPaper('a4', 'portrait')
            ->stream($header->complain_no . '.pdf');
    }
}
