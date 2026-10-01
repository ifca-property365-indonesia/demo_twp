<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\TicketHd;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use DataTables;
use PDF;

class DashController extends Controller
{
    public function index(Request $request, $pdf = null)
    {
        $m = __('admin/dashboard.months_short');

        $selected_year = date('Y');
        $selected_month = date('n');

        /*
        |--------------------------------------------------------------------------
        | YEAR DROPDOWN
        |--------------------------------------------------------------------------
        */

        $usage_years = DB::connection('ifcapb')->select("
            SELECT DISTINCT
                YEAR(read_date) AS Yearly
            FROM mgr.pm_meter_his
            WHERE
                meter_cd LIKE 'E%'
                AND entity_cd = '01'
                AND project_no = '01'
            ORDER BY
                YEAR(read_date) DESC
        ");

        $usage_years = collect($usage_years)
            ->pluck('Yearly')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | MONTH DROPDOWN
        |--------------------------------------------------------------------------
        */

        $usage_months = $m;

        /*
        |--------------------------------------------------------------------------
        | GRAPH 2 : SERVICE STATUS
        |--------------------------------------------------------------------------
        */

        // Status work order dari mgr.sv_entry_hd (complain_source = TWP), seluruh data (tanpa batas bulan/tahun):
        // A = Assigned, P = Process, F = Confirm, C = Closed, X = Cancelled.
        // Status lain (mis. R / O) tidak ditampilkan; Total = jumlah kelima status tersebut.
        $row = DB::connection('ifcapb')->selectOne("
            SELECT
                SUM(CASE WHEN RTRIM(hd.status) = 'A' THEN 1 ELSE 0 END) AS assigned,
                -- Process hanya status P. Dulu S, M, Z, Y ikut dihitung sebagai Process;
                -- sengaja dihilangkan. Kalau perlu lagi: RTRIM(hd.status) IN ('P','S','M','Z','Y')
                SUM(CASE WHEN RTRIM(hd.status) = 'P' THEN 1 ELSE 0 END) AS process,
                SUM(CASE WHEN RTRIM(hd.status) = 'F' THEN 1 ELSE 0 END) AS confirm,
                SUM(CASE WHEN RTRIM(hd.status) = 'C' THEN 1 ELSE 0 END) AS closed,
                SUM(CASE WHEN RTRIM(hd.status) = 'X' THEN 1 ELSE 0 END) AS cancelled,
                SUM(CASE WHEN RTRIM(hd.status) IN ('A', 'P', 'F', 'C', 'X') THEN 1 ELSE 0 END) AS total_all
            FROM mgr.sv_entry_hd hd
            WHERE hd.entity_cd = '01'
                AND hd.complain_source = ?
        ", [TicketHd::COMPLAIN_SOURCE]);

        // satu kolom saja (bukan per bulan); tabel & chart di view tetap memakai array
        $labels_status = [__('admin/dashboard.all_data')];
        $assigned = [(int) ($row->assigned ?? 0)];
        $process = [(int) ($row->process ?? 0)];
        $confirm = [(int) ($row->confirm ?? 0)];
        $closed = [(int) ($row->closed ?? 0)];
        $cancelled = [(int) ($row->cancelled ?? 0)];
        $total = [(int) ($row->total_all ?? 0)];

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('admin.dash.dashboard', [

            'usage_years' => $usage_years,
            'usage_months' => $usage_months,

            'selected_year' => $selected_year,
            'selected_month' => $selected_month,

            'labels_status' => json_encode($labels_status),
            'assigned' => json_encode($assigned),
            'process' => json_encode($process),
            'confirm' => json_encode($confirm),
            'closed' => json_encode($closed),
            'cancelled' => json_encode($cancelled),
            'total' => json_encode($total),
        ]);
    }
    public function usageData(Request $request)
    {
        $m = __('admin/dashboard.months_short');

        $selected_year = $request->get('year', date('Y'));
        $selected_month = $request->get('month', date('n'));
        $selected_category = $request->get('category', 'E');

        $sql = "
            SELECT
                MONTH(a.read_date) AS Monthly,
                YEAR(a.read_date) AS Yearly,
                SUM(b.usage) AS usages,
                SUM(b.usage_high) AS usage_highs
            FROM mgr.pm_meter_his a
            INNER JOIN mgr.pm_meter_dtl_his b ON
                a.entity_cd = b.entity_cd
                AND a.project_no = b.project_no
                AND a.read_date = b.read_date
            WHERE
                a.meter_cd LIKE ?
                AND a.entity_cd = '01'
                AND a.project_no = '01'
                AND YEAR(a.read_date) = ?
                AND MONTH(a.read_date) = ?
            GROUP BY
                MONTH(a.read_date),
                YEAR(a.read_date)
            ORDER BY
                YEAR(a.read_date),
                MONTH(a.read_date)
        ";

        $bindings = [
            $selected_category . '%',
            $selected_year,
            $selected_month
        ];

        $dt_Gra = DB::connection('ifcapb')->select($sql, $bindings);

        $labels_usage = [];
        $usage = [];
        $usage_high = [];

        if (count($dt_Gra) > 0) {

            foreach ($dt_Gra as $row) {

                $labels_usage[] =
                    $m[$row->Monthly] . ' ' . $row->Yearly;

                $usage[] =
                    $row->usages ?? 0;

                $usage_high[] =
                    $row->usage_highs ?? 0;
            }

        } else {

            $labels_usage[] =
                $m[$selected_month] . ' ' . $selected_year;

            $usage[] = 0;
            $usage_high[] = 0;
        }

        return response()->json([
            'labels' => $labels_usage,
            'usage' => $usage,
            'usage_high' => $usage_high
        ]);
    }
    function generatepdf(Request $request)
    {
        $file = $request->file;

        $up = 'data://'.substr($file, 5);
        // getcontent
        $bin = file_get_contents($up);
        if (!is_dir("./images/meterchart")) {
            mkdir("./images/meterchart");
        }
        $nm = './images/meterchart/eu_'.Session::get('Tsuser_id').'.png';
        $na = 'eu_'.Session::get('Tsuser_id').'.png';
        file_put_contents($nm, $bin);
        echo url('admin/dash/export/'.$na);
 
    }
    function export($nm = null)
    {
        $dt_Gra = DB::connection('ifcapb')
        ->select("SELECT MONTH(a.read_date) AS Monthly, YEAR(a.read_date) AS Yearly, SUM(b.usage) AS usages, SUM(b.usage_high) AS usage_highs
            FROM mgr.pm_meter_hdr_debtor a
            INNER JOIN mgr.pm_meter_dtl b ON
            a.entity_cd = b.entity_cd AND
            a.project_no = b.project_no AND
            a.read_date = b.read_date
            INNER JOIN mgr.cf_entity c ON
            a.entity_cd = c.entity_cd
            INNER JOIN mgr.pl_project d ON
            a.entity_cd = d.entity_cd AND
            a.project_no = d.project_no
        WHERE a.pos_flag='P' AND a.meter_type='E' AND a.read_date>DATEADD(m,-4,GETDATE()) AND a.read_date <= GETDATE() 
        GROUP BY MONTH(a.read_date),YEAR(a.read_date)");
        
        if(!empty($dt_Gra)&& !empty($nm))
        {
            $le = '';
            foreach ($dt_Gra as $Eusage) {
                $mn = __('admin/dashboard.months_short.' . (int) $Eusage->Monthly) .' '.$Eusage->Yearly;
                $le.='<tr class="odd">';
                $le.='<td>'.$mn.'</td>';
                $le.='<td>'.number_format($Eusage->usages,2).'</td>';
                $le.='<td>'.number_format($Eusage->usage_highs,2).'</td>';
                $le.='</tr>';
            }
            $nf = 'summary_electric_usage';
       
            $na = url('images/meterchart/'.$nm);
            $content = array('im'=>$na, 'cl'=>$le);
            $pdf = PDF::loadView('admin.dash.exportpdf', $content);
            return $pdf->download($nf.'.pdf');
        }      
    }
    public function getTableOT()
    {
         
        $query = DB::connection('ifcaadm')->select("SELECT @rownum := @rownum + 1 AS row_number, a.id,b.tenant_no,a.lot_no,a.start_overtime,a.end_overtime,a.description,a.status,a.approved FROM ot_trx a join pm_tenancy b on a.id_tenancy = b.id, (SELECT @rownum := 0) r where a.status not in ('X') order by start_overtime desc");

        return DataTables::of($query)->make(true);
    }

    public function getTableTicket()
    {
        // work order di mgr.sv_entry_hd (lihat App\Support\TicketHd)
        $query = TicketHd::query()
            ->orderBy('t.reported_date', 'desc')
            ->orderBy('t.report_no', 'desc')
            ->get();
        $query = TicketHd::withPictures($query);   // picture_url untuk tombol lihat gambar

        foreach ($query as $i => $row) {
            $row->row_number = $i + 1;
            $row->categoryname = $row->category_desc;
        }

        return DataTables::of($query)->make(true);
    }
    
}
