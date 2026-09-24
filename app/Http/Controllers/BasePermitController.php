<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DataTables;
use PDF;

/**
 * Permit Letter (Work Permit W, Entry I / Exit O Permit of Goods) — logika bersama
 * portal tenant (App\Http\Controllers\Tenant\PermitController) dan portal admin
 * (App\Http\Controllers\Admin\PermitController). View-nya juga bersama: resources/views/permit/.
 *
 * Semua data di SQL Server (koneksi dblive):
 *   mgr.sv_entry_letter      header (complain_type = W/I/O, complain_no = nomor permit, status)
 *   mgr.permit_letter_hd/dtl detail Work Permit + daftar pekerja
 *   mgr.permit_letter_tools  rincian kegiatan + peralatan / APD Work Permit
 *   mgr.permit_goods_hd/dtl  detail Permit of Goods + daftar barang
 *   mgr.sv_entry_letter_log  log; tiap perubahan status di-INSERT (tidak pernah di-update)
 *   mgr.sv_spec.letter_no    nomor permit berikutnya per entity/project (LP100001, LP100002, ...)
 *
 * Beda portal:
 *   - tenant hanya melihat/mengubah permit miliknya (TenantScope), admin semua tenant;
 *   - saat ubah permit, tenant boleh mengubah bagian 3-6, admin hanya bagian 4-6
 *     (jadwal, pekerja, kegiatan & peralatan);
 *   - admin bisa menetapkan status Approved (Y) / Cancel (X); tanpa itu jadi Modify (M).
 */
abstract class BasePermitController extends Controller
{
    /** Jenis permit (complain_type) -> label. */
    public const TYPES = [
        'W' => 'Work Permit',
        'I' => 'Entry Permit of Goods',
        'O' => 'Exit Permit of Goods',
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

    /** Status yang isinya masih boleh diubah / dibatalkan (belum disetujui atau dibatalkan). */
    public const EDITABLE_STATUSES = ['R', 'M'];

    /** Satu-satunya status yang boleh dicetak ke PDF (Approved). */
    public const PRINTABLE_STATUS = 'Y';

    /** Status yang bisa ditetapkan admin saat mengubah permit. */
    public const ADMIN_STATUSES = [
        'Y' => 'Approve',
        'X' => 'Cancel',
    ];

    /**
     * Pilihan Jam Kerja Work Permit (sesuai form Surat Izin Kerja): kode -> [mulai, selesai].
     * Tidak disimpan sebagai kolom sendiri; diturunkan lagi dari start_time/end_time.
     * 'O' (lain-lain) = jam diisi bebas.
     */
    public const WORK_SHIFTS = [
        'D' => ['10:00', '22:00'],
        'N' => ['22:00', '10:00'],
    ];

    /** Jam operasional pengelola gedung (ditampilkan di form permit). */
    public const OFFICE_HOURS = '08:00 - 17:00';

    protected const AUDIT_USER = 'TWP';

    // ------------------------------------------------------------------
    // Bagian yang berbeda per portal
    // ------------------------------------------------------------------

    /** 'tenant' | 'admin' — dipakai untuk URL dan aturan di view. */
    abstract protected function portal();

    /** Layout blade portal ini. */
    abstract protected function layout();

    /** Tenancy yang boleh dipilih di form (tenant: miliknya, admin: semua yang aktif). */
    abstract protected function tenancies();

    /** Daftar tenant_no yang boleh dilihat; null = semua (admin). */
    abstract protected function tenantNos();

    /** Data pemohon: ['name' => ..., 'email' => ..., 'hp' => ...] atau null. */
    abstract protected function applicant();

    protected function isAdmin()
    {
        return $this->portal() === 'admin';
    }

    /** Batasi query ke permit yang boleh dilihat portal ini. */
    protected function scoped($query, $column = 'debtor_acct')
    {
        $nos = $this->tenantNos();

        return $nos === null ? $query : $query->whereIn($column, $nos);
    }

    /** URL dasar modul permit portal ini, mis. '.../tenant/permit'. */
    protected function base($path = '')
    {
        return url($this->portal() . '/permit' . ($path === '' ? '' : '/' . ltrim($path, '/')));
    }

    // ------------------------------------------------------------------
    // Halaman
    // ------------------------------------------------------------------

    /** Form Request Permit. */
    public function index()
    {
        $tenancies = $this->tenancies();
        $first = $tenancies->first();
        $applicant = $this->applicant();

        return view('permit.form', $this->formData([
            'tenancies' => $tenancies,
            'letter_no' => $first ? $this->currentLetterNo($first->entity_cd, $first->project_no) : '',
            'applicant' => $applicant,
        ]));
    }

    /** Form ubah permit. Bagian 1-2 selalu hanya tampilan; bagian lain tergantung portal. */
    public function edit($doc_no)
    {
        $permit = $this->findPermit($doc_no);

        if (!$permit) {
            abort(404, 'Permit not found.');
        }

        $status = trim((string) $permit['header']->status);
        if (!in_array($status, self::EDITABLE_STATUSES, true)) {
            return redirect($this->base('history'))->with(
                'alert',
                'Permit ' . $doc_no . ' can no longer be changed (status: ' . (self::STATUSES[$status] ?? $status) . ').'
            );
        }

        return view('permit.form', $this->formData([
            'tenancies' => $this->tenancies(),
            'letter_no' => $permit['header']->complain_no,
            'applicant' => $this->applicant(),
            'permit'    => $permit['header'],
            'detail'    => $permit['detail'],
            'lines'     => $permit['lines'],
            'tools'     => $permit['tools'],
            'locked'    => $this->lockedFields($permit['header']->complain_type),
        ]));
    }

    /** Halaman History Permit (isi tabel diambil via table()). */
    public function history()
    {
        return view('permit.history', [
            'layout'    => $this->layout(),
            'portal'    => $this->portal(),
            'is_admin'  => $this->isAdmin(),
            'types'     => self::TYPES,
            'statuses'  => self::STATUSES,
            'editable'  => self::EDITABLE_STATUSES,
            'tenants'   => $this->isAdmin() ? $this->tenancies() : collect(),
        ]);
    }

    /** Data yang dibutuhkan view form (create maupun edit). */
    private function formData(array $extra)
    {
        return array_merge([
            'layout'       => $this->layout(),
            'portal'       => $this->portal(),
            'is_admin'     => $this->isAdmin(),
            'types'        => self::TYPES,
            'statuses'     => self::ADMIN_STATUSES,
            'office_hours' => self::OFFICE_HOURS,
            'work_shifts'  => self::WORK_SHIFTS,
            'tools'        => [],
            'locked'       => [],
        ], $extra);
    }

    /**
     * Field bagian 3 yang tidak boleh diubah portal ini saat update.
     * Admin tidak boleh mengubah bagian 3 sama sekali (hanya bagian 4-6).
     */
    protected function lockedFields($type)
    {
        if (!$this->isAdmin()) {
            return [];
        }

        return $type === 'W'
            ? ['incharge', 'pic_hp', 'contractor', 'job_type']
            : ['owner', 'job_type'];
    }

    // ------------------------------------------------------------------
    // Endpoint AJAX
    // ------------------------------------------------------------------

    /** Nomor permit berikutnya untuk tenancy yang dipilih di form (JSON). */
    public function letterNo($id_tenancy)
    {
        $tenancy = $this->tenancyInScope($id_tenancy);

        return response()->json([
            'letter_no' => $tenancy ? $this->currentLetterNo($tenancy->entity_cd, $tenancy->project_no) : '',
        ]);
    }

    /** Daftar <option> unit milik tenancy yang dipilih. */
    public function lots($id_tenancy)
    {
        $tenancy = $this->tenancyInScope($id_tenancy);

        if (!$tenancy) {
            return response('<option value=""></option>');
        }

        $lots = DB::connection('dblive')
            ->table('mgr.pm_lot AS l')
            ->join('mgr.pm_tenant_lot AS tl', function ($join) {
                $join->on('l.entity_cd', '=', 'tl.entity_cd')
                    ->on('l.project_no', '=', 'tl.project_no')
                    ->on('l.lot_no', '=', 'tl.lot_no');
            })
            ->where('l.entity_cd', $tenancy->entity_cd)
            ->where('l.project_no', $tenancy->project_no)
            ->where('tl.tenant_no', $tenancy->tenant_no)
            ->orderBy('l.lot_no')
            ->select('l.lot_no', 'l.level_no')
            ->get();

        if ($lots->isEmpty()) {
            return response('<option value="">No unit available</option>');
        }

        $html = '<option value=""></option>';
        foreach ($lots as $lot) {
            $html .= '<option data-level="' . e($lot->level_no) . '" value="' . e($lot->lot_no) . '">' . e($lot->lot_no) . '</option>';
        }

        return response($html);
    }

    // ------------------------------------------------------------------
    // Simpan permit baru
    // ------------------------------------------------------------------

    /**
     * Simpan permit (semua jenis). Dipanggil via AJAX, balasan JSON
     * {status: OK|Fail, pesan, permit_no?, errors?}.
     */
    public function save(Request $request)
    {
        $type = $request->input('permit_type');

        $this->applyWorkShift($request, $type);
        $validator = $this->permitValidator($request, $type);

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
                if (isset($rows['tools_table'])) {
                    $db->table($rows['tools_table'])->insert($rows['tools']);
                }
                $this->writeLog($ctx, 'Request created by ' . $this->portal());

                return $ctx['doc_no'];
            });

            return response()->json([
                'status'    => 'OK',
                'pesan'     => self::TYPES[$type] . ' ' . $doc_no . ' submitted successfully.',
                'permit_no' => $doc_no,
            ]);
        } catch (\Throwable $e) {
            return $this->serverError('save', $e, 'Failed to save permit, please try again.');
        }
    }

    // ------------------------------------------------------------------
    // Ubah permit
    // ------------------------------------------------------------------

    /**
     * Simpan perubahan permit. Jenis permit, nomor, tenant, unit dan lantai tidak ikut
     * diubah; admin juga tidak boleh mengubah field bagian 3.
     * Status: tenant -> M (Modify); admin -> Y/X kalau dipilih, selain itu M.
     */
    public function update(Request $request)
    {
        $permit = $this->findPermit($request->input('doc_no'));

        if (!$permit) {
            return $this->fail('Permit not found.', 404);
        }

        $header = $permit['header'];
        $status = trim((string) $header->status);

        if (!in_array($status, self::EDITABLE_STATUSES, true)) {
            return $this->fail('Permit ' . $header->complain_no . ' can no longer be changed (status: ' . (self::STATUSES[$status] ?? $status) . ').', 422);
        }

        // Jenis permit mengikuti data tersimpan, bukan kiriman form.
        $type = $header->complain_type;
        $request->merge(['permit_type' => $type]);

        // Field yang tidak boleh diubah portal ini: pakai nilai yang tersimpan.
        $locked = $this->lockedFields($type);
        $request->merge($this->storedValues($permit, $locked));
        $this->applyWorkShift($request, $type);

        $validator = $this->permitValidator($request, $type, true, $locked);
        if ($validator->fails()) {
            return $this->fail($validator->errors()->first(), 422, $validator->errors()->toArray());
        }

        // Status baru + catatan log
        $newStatus = 'M';
        if ($this->isAdmin()) {
            $chosen = strtoupper(trim((string) $request->input('set_status')));
            if (isset(self::ADMIN_STATUSES[$chosen])) {
                $newStatus = $chosen;
            }
        }
        $remarks = [
            'Y' => 'Approved by admin',
            'X' => 'Cancelled by ' . $this->portal(),
            'M' => 'Modified by ' . $this->portal(),
        ][$newStatus];

        try {
            $ctx = $this->contextOf($permit);

            // floor & jenis tidak berubah: dipakai headerRow() lewat request
            $request->merge(['floor' => $permit['detail']->floor ?? $header->floor]);

            DB::connection('dblive')->transaction(function () use ($ctx, $request, $type, $permit, $newStatus, $remarks) {
                $rows = $type === 'W'
                    ? $this->workPermitRows($ctx, $request)
                    : $this->goodsPermitRows($ctx, $request, $type);

                // Kolom kunci & data pemohon tidak ikut diubah; status diisi nilai baru.
                $lockedHeader = ['entity_cd', 'project_no', 'debtor_acct', 'complain_type', 'complain_no',
                    'reported_by', 'reported_date', 'floor', 'serv_req_by', 'contact_no', 'billing_type',
                    'status', 'complain_source', 'lot_no', 'post_status'];
                $lockedDetail = ['entity_cd', 'project_no', 'doc_no', 'member_email', 'member_name', 'member_hp',
                    'debtor_acct', 'tower', 'floor', 'unit'];

                $db = DB::connection('dblive');
                $keys = $permit['keys'];

                $db->table('mgr.sv_entry_letter')
                    ->where('entity_cd', $keys['entity_cd'])
                    ->where('project_no', $keys['project_no'])
                    ->where('complain_no', $keys['doc_no'])
                    ->update(array_diff_key($rows['header'], array_flip($lockedHeader)) + ['status' => $newStatus]);

                $db->table($rows['detail_table'])->where($keys)
                    ->update(array_diff_key($rows['detail'], array_flip($lockedDetail)));

                // Daftar pekerja / barang ditulis ulang
                $db->table($rows['lines_table'])->where($keys)->delete();
                $db->table($rows['lines_table'])->insert($rows['lines']);

                // Kegiatan & peralatan Work Permit juga ditulis ulang
                if (isset($rows['tools_table'])) {
                    $db->table($rows['tools_table'])->where($keys)->delete();
                    $db->table($rows['tools_table'])->insert($rows['tools']);
                }

                $this->writeLog($ctx, $remarks);
            });

            return response()->json([
                'status'    => 'OK',
                'pesan'     => self::TYPES[$type] . ' ' . $ctx['doc_no'] . ' ' . strtolower(self::STATUSES[$newStatus]) . ' successfully.',
                'permit_no' => $ctx['doc_no'],
            ]);
        } catch (\Throwable $e) {
            return $this->serverError('update', $e, 'Failed to update permit, please try again.');
        }
    }

    /**
     * Batalkan permit dari halaman History (tanpa membuka form).
     * Hanya permit yang belum disetujui / dibatalkan.
     */
    public function cancel(Request $request)
    {
        $permit = $this->findPermit($request->input('doc_no'));

        if (!$permit) {
            return $this->fail('Permit not found.', 404);
        }

        $header = $permit['header'];
        $status = trim((string) $header->status);

        if (!in_array($status, self::EDITABLE_STATUSES, true)) {
            return $this->fail('Permit ' . $header->complain_no . ' can no longer be cancelled (status: ' . (self::STATUSES[$status] ?? $status) . ').', 422);
        }

        try {
            $ctx = $this->contextOf($permit);

            DB::connection('dblive')->transaction(function () use ($ctx, $permit) {
                $keys = $permit['keys'];

                DB::connection('dblive')->table('mgr.sv_entry_letter')
                    ->where('entity_cd', $keys['entity_cd'])
                    ->where('project_no', $keys['project_no'])
                    ->where('complain_no', $keys['doc_no'])
                    ->update([
                        'status'     => 'X',
                        'audit_user' => self::AUDIT_USER,
                        'audit_date' => $ctx['audit_date'],
                    ]);

                $this->writeLog($ctx, 'Cancelled by ' . $this->portal());
            });

            return response()->json([
                'status'    => 'OK',
                'pesan'     => 'Permit ' . $ctx['doc_no'] . ' has been cancelled.',
                'permit_no' => $ctx['doc_no'],
            ]);
        } catch (\Throwable $e) {
            return $this->serverError('cancel', $e, 'Failed to cancel permit, please try again.');
        }
    }

    /** Nilai tersimpan untuk field yang tidak boleh diubah portal ini. */
    private function storedValues(array $permit, array $fields)
    {
        $detail = $permit['detail'];
        $map = [
            'incharge'   => $detail->pic_name ?? null,
            'pic_hp'     => $detail->pic_hp ?? null,
            'contractor' => $detail->kontraktor_name ?? null,
            'job_type'   => $detail->work_type ?? null,
            'work_tool'  => $detail->work_tools ?? null,
            'owner'      => $detail->owner_name ?? null,
        ];

        $values = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $map)) {
                $values[$field] = $map[$field];
            }
        }

        return $values;
    }

    /** Konteks (kunci + data pemohon) dari permit yang sudah tersimpan. */
    private function contextOf(array $permit)
    {
        $header = $permit['header'];
        $detail = $permit['detail'];

        return [
            'entity_cd'    => $header->entity_cd,
            'project_no'   => $header->project_no,
            'debtor_acct'  => $header->debtor_acct,
            'doc_no'       => $header->complain_no,
            'lot_no'       => $header->lot_no,
            'tower'        => $detail->tower ?? null,
            'member_name'  => $detail->member_name ?? $header->serv_req_by,
            'member_email' => $detail->member_email ?? null,
            'member_hp'    => $detail->member_hp ?? $header->contact_no,
            'audit_date'   => now()->format('Y-m-d\TH:i:s'),
        ];
    }

    /** Satu baris log baru (tidak pernah di-update) untuk tiap perubahan status. */
    private function writeLog(array $ctx, $remarks)
    {
        DB::connection('dblive')->table('mgr.sv_entry_letter_log')->insert([
            'entity_cd'   => $ctx['entity_cd'],
            'project_no'  => $ctx['project_no'],
            'complain_no' => $ctx['doc_no'],
            'remarks'     => $remarks,
            'audit_user'  => self::AUDIT_USER,
            'audit_date'  => $ctx['audit_date'],
        ]);
    }

    // ------------------------------------------------------------------
    // Validasi & pembentukan baris
    // ------------------------------------------------------------------

    /**
     * Validator isi form permit. Saat update, Tenant/Unit/Floor (bagian 1-2) tidak ikut
     * dikirim, dan field yang terkunci untuk portal ini tidak divalidasi (nilainya
     * diambil dari data tersimpan).
     */
    private function permitValidator(Request $request, $type, $isUpdate = false, array $locked = [])
    {
        $rules = [
            'permit_type' => ['required', 'in:' . implode(',', array_keys(self::TYPES))],
            'note'        => ['required', 'string', 'max:500'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
        ];

        if (!$isUpdate) {
            $rules += [
                'tenant_no' => ['required', 'integer'],
                'lot_no'    => ['required', 'string', 'max:8'],
                'floor'     => ['required', 'string', 'max:5'],
            ];
        }

        if ($type === 'W') {
            // Jam kerja boleh melewati tengah malam (22.00 - 10.00), jadi end_time
            // cukup berbeda dari start_time, tidak harus lebih besar.
            // Baris kegiatan: tool_activity[i] + tool_name[i] (+ tool_remarks[i] opsional).
            $rules += [
                'incharge'        => ['required', 'string', 'max:50'],
                'pic_hp'          => ['required', 'string', 'max:20'],
                'contractor'      => ['required', 'string', 'max:50'],
                'job_type'        => ['required', 'string', 'max:50'],
                'work_shift'      => ['required', 'in:' . implode(',', array_keys(self::WORK_SHIFTS)) . ',O'],
                'start_time'      => ['required', 'date_format:H:i,H:i:s'],
                'end_time'        => ['required', 'date_format:H:i,H:i:s', 'different:start_time'],
                'worker_name'     => ['required', 'array', 'min:1'],
                'worker_name.*'   => ['required', 'string', 'max:50'],
                'tool_activity'   => ['required', 'array', 'min:1'],
                'tool_activity.*' => ['required', 'string', 'max:100'],
                'tool_name'       => ['required', 'array', 'size:' . count((array) $request->input('tool_activity'))],
                'tool_name.*'     => ['required', 'string', 'max:100'],
                'tool_remarks'    => ['nullable', 'array'],
                'tool_remarks.*'  => ['nullable', 'string', 'max:255'],
            ];
        } else {
            // Jam keluar/masuk barang juga boleh lewat tengah malam (aturan: 22.00 - 10.00).
            // Baris barang: item_name[i] + item_qty[i] (+ item_remarks[i] opsional).
            $rules += [
                'owner'          => ['required', 'string', 'max:50'],
                'job_type'       => ['required', 'string', 'max:50'],
                'start_time'     => ['required', 'date_format:H:i,H:i:s'],
                'end_time'       => ['required', 'date_format:H:i,H:i:s', 'different:start_time'],
                'sender_name'    => ['required', 'string', 'max:50'],
                'sender_id_no'   => ['required', 'string', 'max:30'],
                'sender_address' => ['required', 'string', 'max:255'],
                'sender_hp'      => ['required', 'string', 'max:20'],
                'vehicle_type'   => ['required', 'string', 'max:30'],
                'vehicle_no'     => ['required', 'string', 'max:10'],
                'item_name'      => ['required', 'array', 'min:1'],
                'item_name.*'    => ['required', 'string', 'max:100'],
                'item_qty'       => ['required', 'array', 'size:' . count((array) $request->input('item_name'))],
                'item_qty.*'     => ['required', 'string', 'max:20'],
                'item_remarks'   => ['nullable', 'array'],
                'item_remarks.*' => ['nullable', 'string', 'max:255'],
            ];
        }

        $rules = array_diff_key($rules, array_flip($locked));

        return Validator::make($request->all(), $rules, [], [
            'tenant_no'     => 'tenant',
            'lot_no'        => 'unit',
            'incharge'      => 'person in charge',
            'pic_hp'        => 'office phone / HP',
            'contractor'    => 'contractor name',
            'work_shift'    => 'working hours',
            'tool_activity'   => 'activity',
            'tool_activity.*' => 'activity',
            'tool_name'       => 'tools / PPE',
            'tool_name.*'     => 'tools / PPE',
            'tool_remarks.*'  => 'remarks',
            'worker_name'   => 'worker',
            'worker_name.*' => 'worker name',
            'owner'          => 'owner / tenant name',
            'sender_name'    => 'sender / pickup name',
            'sender_id_no'   => 'ID card / driving license no.',
            'sender_address' => 'address',
            'sender_hp'      => 'phone number',
            'vehicle_type'   => 'vehicle type',
            'vehicle_no'     => 'vehicle number',
            'item_name'      => 'item',
            'item_name.*'    => 'item name',
            'item_qty'       => 'quantity',
            'item_qty.*'     => 'quantity',
            'item_remarks.*' => 'remarks',
        ]);
    }

    /** Baris-baris Work Permit: header sv_entry_letter, permit_letter_hd, permit_letter_dtl. */
    private function workPermitRows(array $ctx, Request $request)
    {
        $start_date = $this->fmtDate($request->start_date);
        $end_date   = $this->fmtDate($request->end_date);
        $start_time = substr($request->start_time, 0, 5);
        $end_time   = substr($request->end_time, 0, 5);

        // Kegiatan + peralatan / APD per baris; kolom work_tool(s) lama diisi ringkasannya.
        $toolNames = array_values((array) $request->tool_name);
        $remarks   = array_values((array) $request->tool_remarks);
        $tools = [];
        foreach (array_values((array) $request->tool_activity) as $i => $activity) {
            $remark = trim((string) ($remarks[$i] ?? ''));
            $tools[] = $this->lineRow($ctx) + [
                'activity'  => trim($activity),
                'tool_name' => trim((string) ($toolNames[$i] ?? '')),
                'remarks'   => $remark === '' ? null : $remark,
            ];
        }
        $summary = mb_substr(implode(', ', array_unique(array_column($tools, 'tool_name'))), 0, 255);

        $header = $this->headerRow($ctx, 'W', $request) + [
            'pj_name'    => $request->incharge,
            'contractor' => $request->contractor,
            'job_type'   => $request->job_type,
            'work_tool'  => $summary,
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
            'pic_hp'          => $request->pic_hp,
            'kontraktor_name' => $request->contractor,
            'tower'           => $ctx['tower'],
            'floor'           => $request->floor,
            'unit'            => $ctx['lot_no'],
            'work_type'       => $request->job_type,
            'work_tools'      => $summary,
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
        foreach ((array) $request->worker_name as $name) {
            $lines[] = $this->lineRow($ctx) + ['staff_name' => trim($name)];
        }

        return [
            'header'       => $header,
            'detail_table' => 'mgr.permit_letter_hd',
            'detail'       => $detail,
            'lines_table'  => 'mgr.permit_letter_dtl',
            'lines'        => $lines,
            'tools_table'  => 'mgr.permit_letter_tools',
            'tools'        => $tools,
        ];
    }

    /** Baris-baris Entry/Exit Permit of Goods: header sv_entry_letter, permit_goods_hd, permit_goods_dtl. */
    private function goodsPermitRows(array $ctx, Request $request, $type)
    {
        $start_time = substr($request->start_time, 0, 5);
        $end_time   = substr($request->end_time, 0, 5);

        // company_name tidak ada lagi di form (form kertas tidak memuatnya) -> null.
        $header = $this->headerRow($ctx, $type, $request) + [
            'owner_name' => $request->owner,
            'job_type'   => $request->job_type,
            'vehicle_no' => $request->vehicle_no,
            'start_time' => $start_time,
            'end_time'   => $end_time,
        ];

        $detail = [
            'entity_cd'      => $ctx['entity_cd'],
            'project_no'     => $ctx['project_no'],
            'doc_no'         => $ctx['doc_no'],
            'member_email'   => $ctx['member_email'],
            'member_name'    => $ctx['member_name'],
            'member_hp'      => $ctx['member_hp'],
            'debtor_acct'    => $ctx['debtor_acct'],
            'owner_name'     => $request->owner,
            'tower'          => $ctx['tower'],
            'floor'          => $request->floor,
            'unit'           => $ctx['lot_no'],
            'start_date'     => $this->fmtDate($request->start_date),
            'end_date'       => $this->fmtDate($request->end_date),
            'start_time'     => $start_time,
            'end_time'       => $end_time,
            'sender_name'    => $request->sender_name,
            'sender_id_no'   => $request->sender_id_no,
            'sender_address' => $request->sender_address,
            'sender_hp'      => $request->sender_hp,
            'vehicle_type'   => $request->vehicle_type,
            'vehicle_no'     => $request->vehicle_no,
            'work_type'      => $request->job_type,
            'note'           => $request->note,
            'audit_user'     => self::AUDIT_USER,
            'audit_date'     => $ctx['audit_date'],
        ];

        // Jenis barang + jumlah + keterangan (item_descs) per baris.
        $qtys    = array_values((array) $request->item_qty);
        $remarks = array_values((array) $request->item_remarks);
        $lines = [];
        foreach (array_values((array) $request->item_name) as $i => $name) {
            $remark = trim((string) ($remarks[$i] ?? ''));
            $lines[] = $this->lineRow($ctx) + [
                'item_name'  => trim($name),
                'item_qty'   => trim((string) ($qtys[$i] ?? '')),
                'item_descs' => $remark === '' ? null : $remark,
            ];
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
            'complain_source' => 'TWP PERMIT',
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
     * Data bersama permit baru: tenancy yang dipilih (dicek cakupannya), tower dari unit,
     * dan data pemohon. Nomor dokumen diambil belakangan di dalam transaksi.
     * Mengembalikan array, atau string pesan error kalau ada yang tidak valid.
     */
    private function permitContext($id_tenancy, $lot_no)
    {
        $applicant = $this->applicant();
        if (!$applicant) {
            return 'Applicant data not found.';
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
            'member_name'  => mb_substr((string) $applicant['name'], 0, 50),
            'member_email' => mb_substr((string) $applicant['email'], 0, 60),
            'member_hp'    => mb_substr((string) $applicant['hp'], 0, 20),
            'audit_date'   => now()->format('Y-m-d\TH:i:s'),   // ISO 8601 dengan 'T': tidak terpengaruh DATEFORMAT
        ];
    }

    /**
     * Header + detail + daftar satu permit yang boleh dilihat portal ini.
     * null kalau tidak ada / di luar cakupan.
     */
    protected function findPermit($doc_no)
    {
        $db = DB::connection('dblive');

        $header = $this->scoped(
            $db->table('mgr.sv_entry_letter')->where('complain_no', $doc_no),
            'debtor_acct'
        )->whereIn('complain_type', array_keys(self::TYPES))->first();

        if (!$header) {
            return null;
        }

        $keys = [
            'entity_cd'  => $header->entity_cd,
            'project_no' => $header->project_no,
            'doc_no'     => $header->complain_no,
        ];

        $tools = [];

        if ($header->complain_type === 'W') {
            $detail = $db->table('mgr.permit_letter_hd')->where($keys)->first();
            $lines  = $db->table('mgr.permit_letter_dtl')->where($keys)->orderBy('rowID')->pluck('staff_name')->all();
            $tools  = $db->table('mgr.permit_letter_tools')->where($keys)->orderBy('rowID')
                ->get(['activity', 'tool_name', 'remarks'])
                ->map(function ($t) {
                    return [
                        'activity'  => trim((string) $t->activity),
                        'tool_name' => trim((string) $t->tool_name),
                        'remarks'   => trim((string) $t->remarks),
                    ];
                })->all();

            // Permit lama (sebelum ada permit_letter_tools): satu baris dari work_type + work_tools.
            if (!$tools && $detail && trim((string) $detail->work_tools) !== '') {
                $tools = [[
                    'activity'  => trim((string) $detail->work_type),
                    'tool_name' => trim((string) $detail->work_tools),
                    'remarks'   => '',
                ]];
            }
        } else {
            // Barang: ['item_name', 'item_qty', 'remarks']. Data lama mengisi item_descs sama
            // dengan item_name, jadi yang seperti itu tidak dianggap keterangan.
            $detail = $db->table('mgr.permit_goods_hd')->where($keys)->first();
            $lines  = $db->table('mgr.permit_goods_dtl')->where($keys)->orderBy('rowID')
                ->get(['item_name', 'item_qty', 'item_descs'])
                ->map(function ($t) {
                    $name  = trim((string) $t->item_name);
                    $descs = trim((string) $t->item_descs);
                    return [
                        'item_name' => $name,
                        'item_qty'  => trim((string) $t->item_qty),
                        'remarks'   => $descs === $name ? '' : $descs,
                    ];
                })->all();
        }

        // lines = nama pekerja (W) atau baris barang (I/O); tools = kegiatan & peralatan (W).
        return ['header' => $header, 'detail' => $detail, 'lines' => $lines, 'tools' => $tools, 'keys' => $keys];
    }

    /**
     * Jam Kerja Work Permit: pilihan 10.00-22.00 / 22.00-10.00 mengisi start_time & end_time
     * dari WORK_SHIFTS (jam kiriman form diabaikan); 'O' (lain-lain) memakai jam yang diisi.
     */
    private function applyWorkShift(Request $request, $type)
    {
        if ($type !== 'W') {
            return;
        }

        $shift = strtoupper(trim((string) $request->input('work_shift')));
        if (isset(self::WORK_SHIFTS[$shift])) {
            $request->merge([
                'start_time' => self::WORK_SHIFTS[$shift][0],
                'end_time'   => self::WORK_SHIFTS[$shift][1],
            ]);
        }
    }

    /** Kode Jam Kerja (D / N / O = lain-lain) dari jam tersimpan. */
    public static function workShiftOf($start_time, $end_time)
    {
        $times = [substr(trim((string) $start_time), 0, 5), substr(trim((string) $end_time), 0, 5)];

        foreach (self::WORK_SHIFTS as $code => $range) {
            if ($range === $times) {
                return $code;
            }
        }

        return 'O';
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

    /** Baris pm_tenancy yang dipilih di combo (value = pm_tenancy.id), hanya kalau masuk cakupan. */
    protected function tenancyInScope($id_tenancy)
    {
        $tenancy = DB::table('pm_tenancy')->where('id', (int) $id_tenancy)->first();

        if (!$tenancy) {
            return null;
        }

        $nos = $this->tenantNos();

        return ($nos === null || in_array((string) $tenancy->tenant_no, $nos, true)) ? $tenancy : null;
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

    /** Error tak terduga: dicatat di log, detailnya hanya ditampilkan saat APP_DEBUG. */
    private function serverError($method, \Throwable $e, $message)
    {
        Log::error(static::class . '::' . $method . ': ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return $this->fail(config('app.debug') ? 'An error occurred: ' . $e->getMessage() : $message, 500);
    }

    // ------------------------------------------------------------------
    // Tabel & cetak
    // ------------------------------------------------------------------

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
            ->whereIn('sel.complain_type', array_keys(self::TYPES))
            ->select(
                'sel.complain_no as complain_no',
                'sel.complain_type as complain_type',
                'sel.debtor_acct as debtor_acct',
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

        $this->scoped($permit, 'sel.debtor_acct');

        // Filter opsional, boleh dipakai sendiri-sendiri atau digabung. Kalau semuanya kosong
        // (kondisi saat halaman pertama dibuka) seluruh permit dalam cakupan ikut tampil.
        $permit_no   = trim((string) $request->permit_no);
        $permit_type = strtoupper(trim((string) $request->permit_type));
        $status      = strtoupper(trim((string) $request->status));
        $tenant      = trim((string) $request->tenant_no);
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

        if ($this->isAdmin() && $tenant !== '') {
            $permit->where('sel.debtor_acct', $tenant);
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

        // escapeColumns([]): isi kolom dikirim apa adanya, karena history.blade sudah
        // meng-escape tiap kolom saat render (esc/dash). Kalau server juga meng-escape,
        // '&' tampil sebagai '&amp;'.
        return DataTables::of($query)
            ->escapeColumns([])
            ->addIndexColumn()
            ->order(function ($query) use ($request) {
                // Kalau user klik header kolom, ikuti urutan itu. Kalau tidak
                // (halaman baru dibuka), urutkan dari tanggal mulai terbaru, lalu
                // nomor permit terbesar untuk tanggal yang sama.
                $orders = (array) $request->input('order', []);
                if (empty($orders)) {
                    $query->orderBy('start_date', 'desc')->orderBy('complain_no', 'desc');
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
     * Tombol Print di tabel: halaman HTML kecil yang menampilkan PDF (printPdf) dalam iframe.
     * PDF sendiri tidak bisa membawa ikon, jadi tanpa halaman ini tab browser memakai
     * favicon root domain (XAMPP), bukan ikon TWP.
     */
    public function printPage($doc_no)
    {
        $header = $this->printablePermit($doc_no)['header'];

        return view('permit.print_frame', [
            'title'   => trim($header->complain_no),
            'pdf_url' => $this->base('pdf/' . rawurlencode(trim($header->complain_no))),
        ]);
    }

    /** Permit yang boleh dicetak portal ini: hanya yang sudah Approved (Y). */
    private function printablePermit($doc_no)
    {
        $permit = $this->findPermit($doc_no);

        if (!$permit) {
            abort(404, 'Permit not found.');
        }

        $header = $permit['header'];
        $status = trim((string) $header->status);

        if ($status !== self::PRINTABLE_STATUS) {
            abort(403, 'Permit ' . trim($header->complain_no) . ' cannot be printed (status: '
                . (self::STATUSES[$status] ?? ($status !== '' ? $status : '-')) . '). Only approved permits can be printed.');
        }

        return $permit;
    }

    /** File PDF satu permit (dimuat oleh printPage), hanya yang masuk cakupan portal. */
    public function printPdf($doc_no)
    {
        $permit = $this->printablePermit($doc_no);
        $header = $permit['header'];

        // Nama pengelola gedung & project untuk kop surat.
        $tenancy = DB::table('pm_tenancy')
            ->where('tenant_no', $header->debtor_acct)
            ->where('entity_cd', trim($header->entity_cd))
            ->where('project_no', trim($header->project_no))
            ->first();

        $tenant = DB::table('tenant')->where('tenant_no_df', $header->debtor_acct)->first();

        // Work Permit dicetak dengan format form "Surat Izin Kerja / Working Permit".
        if ($header->complain_type === 'W') {
            return PDF::loadView('permit.print_work', [
                'header'  => $header,
                'detail'  => $permit['detail'],
                'workers' => count($permit['lines']),
                'tools'   => $permit['tools'],
                'tenancy' => $tenancy,
                'tenant'  => $tenant,
                'logo'    => base_path('img/logoweb/carstensz-logo-print.jpg'),
            ])
                ->setPaper('a4', 'portrait')
                ->stream($header->complain_no . '.pdf');
        }

        // Entry / Exit Permit of Goods: form "Surat Izin Keluar / Masuk Barang".
        return PDF::loadView('permit.print_goods', [
            'header'  => $header,
            'detail'  => $permit['detail'],
            'items'   => $permit['lines'],
            'tenancy' => $tenancy,
            'tenant'  => $tenant,
            'type'    => $header->complain_type,
            'logo'    => base_path('img/logoweb/carstensz-logo-print.jpg'),
        ])
            ->setPaper('a4', 'portrait')
            ->stream($header->complain_no . '.pdf');
    }
}
