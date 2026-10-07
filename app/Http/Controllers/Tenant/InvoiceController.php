<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Support\TenantScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PDF;
// use Barryvdh\DomPDF\Facade as PDF;

class InvoiceController extends Controller
{
    public function proforma()
    {
        $business_no = Session::get('business_no');
        $tenant_no   = Session::get('tenant_df');
        
        $crit = [
            'business_no' => $business_no,
            'tenant_no'   => $tenant_no
        ];

        $dataTenancy = DB::table('mgr.pm_tenancy')
            ->where($crit)
            ->first();

        $list_bill = '';
        $footer_bill = '';

        if ($dataTenancy) {

            $entity  = $dataTenancy->entity_cd;
            $project = $dataTenancy->project_no;

            $bill = DB::connection('dblive')
                ->table('mgr.ar_bill')
                ->whereIn('entity_cd', TenantScope::entityCds())
                ->whereIn('project_no', TenantScope::projectNos())
                ->whereIn('debtor_acct', TenantScope::tenantNos())
                ->orderBy('doc_date', 'desc')
                ->get();

            $no = 1;
            $totalOutstanding = 0;

            foreach ($bill as $row) {

                $outstanding = ($row->base_amt ?? 0) + ($row->tax_amt ?? 0);

                $totalOutstanding += $outstanding;

                // Nama file PDF dari mgr.ar_bill.file_attachment (mis. 00060-3_PT_Kreasi_Seni_Jaya_202605.pdf),
                // dibuka lewat proformaPdf() yang mengambil file dari FTP (FTP_* di .env).
                $fileName = trim((string) ($row->file_attachment ?? ''));
                $check = $fileName === '' ? '' : '
                    <a href="' . e(route('invoice.proforma.pdf', ['file' => $fileName])) . '"
                    target="_blank"
                    class="btn btn-sm btn-success">
                        <i class="cil-file"></i>
                        ' . e(__('tenant/invoice.pdf')) . '
                    </a>
                ';

                $list_bill .= '
                    <tr>
                        <td class="text-center">'.$no++.'</td>
                        <td>'.$row->proforma_no.'</td>
                        <td>'.date('d F Y', strtotime($row->doc_date)).'</td>
                        <td>'.date('d F Y', strtotime($row->due_date)).'</td>
                        <td>'.$row->descs.'</td>
                        <td>'.date('F Y', strtotime($row->trx_date)).'</td>
                        <td class="text-end">'.number_format($outstanding, 2).'</td>
                        <td class="text-center">'.$check.'</td>
                    </tr>';
            }

            $footer_bill = '
                <tr style="font-weight:bold;background:#f5f6fa">
                    <td colspan="6" class="text-end">'.e(__('tenant/invoice.total')).'</td>
                    <td class="text-end">'.number_format($totalOutstanding,2).'</td>
                </tr>';
        }

        return view('tenant.invoice.proforma', compact(
            'list_bill',
            'footer_bill',
            'totalOutstanding',
            'tenant_no'
        ));
    }

    public function index()
    {
        $business_no = Session::get('business_no');
        $tenant_no   = Session::get('tenant_df');

        $crit = [
            'business_no' => $business_no,
            'tenant_no'   => $tenant_no
        ];

        $dataTenancy = DB::table('mgr.pm_tenancy')
            ->where($crit)
            ->first();

        $list_bill = '';
        $footer_bill = '';

        if ($dataTenancy) {

            $entity  = $dataTenancy->entity_cd;
            $project = $dataTenancy->project_no;

            $data = DB::connection('dblive')
                ->table('mgr.ar_ledger')
                ->whereIn('entity_cd', TenantScope::entityCds())
                ->whereIn('project_no', TenantScope::projectNos())
                ->whereIn('class', ['I', 'N'])
                ->where('mbal_amt', '>', 0)
                ->whereIn('debtor_acct', TenantScope::tenantNos())
                ->get();
            $no = 1;
            $totalOutstanding = 0;

            foreach ($data as $row) {

                $totalOutstanding += $row->fdoc_amt;

                $list_bill .= '
                    <tr>
                        <td class="text-center">'.$no++.'</td>
                        <td>'.$row->doc_no.'</td>
                        <td>'.date('d F Y', strtotime($row->doc_date)).'</td>
                        <td>'.date('d F Y', strtotime($row->void_date)).'</td>
                        <td>'.$row->descs.'</td>
                        <td>'.date('F Y', strtotime($row->trx_date)).'</td>
                        <td class="text-end">'.number_format($row->fdoc_amt, 2).'</td>
                    </tr>';
            }

            $footer_bill = '
                <tr style="font-weight:bold;background:#f5f6fa">
                    <td colspan="6" class="text-end">'.e(__('tenant/invoice.total')).'</td>
                    <td class="text-end">'.number_format($totalOutstanding,2).'</td>
                </tr>';
        }

        return view('tenant.invoice.index', compact(
            'list_bill',
            'footer_bill',
            'totalOutstanding',
            'tenant_no'
        ));
    }

    /**
     * PDF proforma dari FTP (disk 'ftp'). Hanya file yang tercatat di mgr.ar_bill.file_attachment
     * milik tenant yang login, supaya tenant tidak bisa membuka file tenant lain lewat URL.
     */
    public function proformaPdf($file)
    {
        $file = basename($file);

        $owned = DB::connection('dblive')
            ->table('mgr.ar_bill')
            ->whereIn('entity_cd', TenantScope::entityCds())
            ->whereIn('project_no', TenantScope::projectNos())
            ->whereIn('debtor_acct', TenantScope::tenantNos())
            ->where('file_attachment', $file)
            ->exists();

        if (!$owned || !Storage::disk('ftp')->exists($file)) {
            abort(404, __('tenant/invoice.file_not_found'));
        }

        $content = Storage::disk('ftp')->get($file);

        return response($content, 200)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'inline; filename="' . $file . '"'
            );
    }
}