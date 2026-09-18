<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use DataTables;
use PDF;

class HistoryController extends Controller
{

    public function ticket(){
       
        $sqlad = "SELECT distinct debtor_acct,name from mgr.v_ticket_history WHERE status IN ('C','X','F','A')  ORDER BY name,debtor_acct asc";        
        $dtDebtor = DB::connection('ifcapb')->select($sqlad);    
        $content = array(
            'datadebtor'=>$dtDebtor);
        
        return view('admin.history.ticket',$content);
    } 
    public function getTableTicket(Request $request)
    {

        $debtor = $request->debtor_acct;
        if(empty($debtor)){
            $debtor='';
        }

        $date_end = $request->date_end;
        if(empty($date_end)){
            $date_end=date('Y-m-d 23:59:59', time() + 86400);  
        }else{
            $date_end=$date_end." 23:59:59";
        }

        $date_start = $request->date_start;
        if(empty($date_start)){
            $date_start=date('Y-m-01 00:00:00', strtotime("-12 months"));
        }else{
            $date_start=$date_start." 00:00:00";
        }
        $where = '';

        if($debtor!='' || !empty($debtor))
        {
            $where="AND debtor_acct='".$debtor."' ".$where;
        }

        $sql ="SELECT ROW_NUMBER() OVER (ORDER BY reported_date desc) AS [row_number], * from mgr.v_ticket_history where status in  ('C','X','F') and reported_date between CONVERT(DATETIME,'".$date_start."',110) and CONVERT(DATETIME,'".$date_end."',110)".$where;
        $query = DB::connection('ifcapb')->select($sql);
        return DataTables::of($query)->make(true);
    }
    public function overtime(){
       
        $sqlad = "SELECT distinct debtor_acct from mgr.v_overtime_history ORDER BY debtor_acct asc";        
        $dtDebtor = DB::connection('ifcapb')->select($sqlad);    
        $content = array(
            'datadebtor'=>$dtDebtor);
        
        return view('admin.history.overtime',$content);
    } 
    public function getTableOT(Request $request)
    {

        $debtor = $request->debtor_acct;
        if(empty($debtor)){
            $debtor='';
        }

        $date_end = $request->date_end;
        if(empty($date_end)){
            $date_end=date('Y-m-d 23:59:59', time() + 86400);  
        }else{
            $date_end=$date_end." 23:59:59";
        }

        $date_start = $request->date_start;
        if(empty($date_start)){
            $date_start=date('Y-m-01 00:00:00', strtotime("-12 months"));
        }else{
            $date_start=$date_start." 00:00:00";
        }
        $where = '';

        if($debtor!='' || !empty($debtor))
        {
            $where=" AND debtor_acct='".$debtor."' ".$where;
        }

        $sql ="SELECT ROW_NUMBER() OVER (ORDER BY begin_date desc) AS [row_number], * from mgr.v_overtime_history where  begin_date between CONVERT(DATETIME,'".$date_start."',110) and CONVERT(DATETIME,'".$date_end."',110)".$where ;
        $query = DB::connection('ifcapb')->select($sql);
        return DataTables::of($query)->make(true);
    }
    public function getTableLog(Request $request)
    {

        $date_end = $request->date_end;
        if(empty($date_end)){
            $date_end=date('Y-m-d 23:59:59', time() + 86400);  
        }else{
            $date_end=$date_end." 23:59:59";
        }

        $date_start = $request->date_start;
        if(empty($date_start)){
            $date_start=date('Y-m-01 00:00:00', strtotime("-12 months"));
        }else{
            $date_start=$date_start." 00:00:00";
        }
        $where = '';
        $sql ="SELECT * FROM (
            SELECT 
                @rownum := @rownum + 1 AS row_number,idforeign,logintime,ipaddress,name,email 
            FROM log_login join tenant on tenant.id = log_login.idforeign
            JOIN (SELECT @rownum := 0) r
            ) sub
        where sub.logintime between '".$date_start."' and '".$date_end."' ".$where."";
        $query = DB::connection('ifcaadm')->select($sql);
        return DataTables::of($query)->make(true);
        
    }
    public function dlpdf(Request $request)
    {
        $type = $request->type;
        $date_end = $request->date_end;
        $date_start = $request->date_start;
        $debtor = $request->debtor_acct;
        Session::put('gentype', $type);
        Session::put('date_end', $date_end);
        Session::put('date_start', $date_start);
        Session::put('debtor', $debtor);
        echo url('admin/history/export/'.$type);
    }
    public function export(Request $request)
    {
        $type = $request->type;
        $date_end = Session::get('date_end');
        if(empty($date_end)){
            $date_end=date('Y-m-d 23:59:59', time() + 86400);  
        }else{
            $aa = explode("/",$date_end);
            $date_end = $aa[2]."-".$aa[1]."-".$aa[0]." 23:59:59";
        }
        $date_start = Session::get('date_start');
        if(empty($date_start)){
            $date_start=date('Y-m-01 00:00:00', strtotime("-12 months"));
        }else{
            $aa = explode("/",$date_start);
            $date_start = $aa[2]."-".$aa[1]."-".$aa[0]." 00:00:00";
        }
        if(!empty($type))
        {
            switch ($type) {
                case 'ticket':
                    $list_log = '';$i=1;
                    $debtor = Session::get('debtor');
                    if(empty($debtor)){
                        $debtor='';
                    }
                    $where = '';

                    if($debtor!='' || !empty($debtor))
                    {
                        $where="AND debtor_acct='".$debtor."' ".$where;
                    }

                    
                    $sql ="SELECT ROW_NUMBER() OVER (ORDER BY reported_date desc) AS [row_number], * from mgr.v_ticket_history where status in  ('C','X','F') and reported_date between CONVERT(DATETIME,'".$date_start."',110) and CONVERT(DATETIME,'".$date_end."',110)".$where;
                    $dt_ticket = DB::connection('ifcapb')->select($sql);
                    if(!empty($dt_ticket))
                    {
                        
                        foreach ($dt_ticket as $ticket) {
                            $descs = '';
                            $status = $ticket->status;
                            if($status=='R'){
                                $descs = 'Open';
                            }else if($status=='Z'){
                                $descs = 'Process';
                            }else if($status=='Y'){
                                $descs = 'Process';
                            }else{
                                $descs = '';
                            }
                            $list_log.='<tr role="row" class="odd">';
                            $list_log.='<td style="padding: 4px">'.$i.'</td>';
                            $list_log.='<td style="padding: 4px">'.$ticket->complain_no.'</td>';
                            $list_log.='<td style="padding: 4px">'.$ticket->categoryname.'</td>';
                            $list_log.='<td style="padding: 4px">'.$ticket->name.'</td>';
                            $list_log.='<td style="padding: 4px">'.$ticket->work_requested.'</td>';
                            $list_log.='<td style="padding: 4px">'.$ticket->reported_date.'</td>';
                            $list_log.='<td style="padding: 4px">'.$ticket->serv_req_by.'</td>';
                            $list_log.='<td style="padding: 4px"> '.$ticket->lot_no.'</td>';
                            $list_log.='<td style="padding: 4px">'. $descs. '</td>';
                            $list_log.='</tr>';
                            $i++;
                        }
                    }else{
                        $list_log.='<tr class="odd">';
                        $list_log.='<td colspan="9" style="text-align:center">No Data Available</td>';
                        $list_log.='</tr>';
                    }
                    
                    $content = array('listD'=>$list_log);
                    $nf = 'history_tiket';
                    $pdf = PDF::loadView('admin.history.expticket', $content);
                    return $pdf->setPaper("a4","potrait")->stream($nf.'.pdf');
                    break;
                case 'log':
                    $where = '';
                    $sql ="SELECT * FROM (
                        SELECT 
                            @rownum := @rownum + 1 AS row_number,idforeign,logintime,ipaddress,name,email 
                        FROM log_login join tenant on tenant.id = log_login.idforeign
                        JOIN (SELECT @rownum := 0) r
                        ) sub
                    where sub.logintime between '".$date_start."' and '".$date_end."' ".$where."";
                    $dtUsers = DB::connection('ifcaadm')->select($sql);
                    $list_log = '';
                    if(!empty($dtUsers))
                    {
                        foreach ($dtUsers as $logUsers) {
                            $list_log.='<tr class="odd">';
                            $list_log.='<td>'.date('d M Y H:i:s',strtotime($logUsers->logintime)).'</td>';
                            $list_log.='<td>'.$logUsers->name.'</td>';
                            $list_log.='<td>'.$logUsers->ipaddress.'</td>';
                            $list_log.='</tr>';
                        }
                    }else{
                        $list_log.='<tr class="odd">';
                        $list_log.='<td colspan="3" style="text-align:center">No Data Available</td>';
                        $list_log.='</tr>';
                    }
                    $content = array('listD'=>$list_log);
                    $nf = 'log_users';
                    $pdf = PDF::loadView('admin.history.explog', $content);
                    return $pdf->stream($nf.'.pdf');
                    break;
                case 'overtime':
                    $debtor = Session::get('debtor');
                    if(empty($debtor)){
                        $debtor='';
                    }
                    $where = '';

                    if($debtor!='' || !empty($debtor))
                    {
                        $where=" AND debtor_acct='".$debtor."' ".$where;
                    }

                    $sql ="SELECT ROW_NUMBER() OVER (ORDER BY begin_date desc) AS [row_number], * from mgr.v_overtime_history where  begin_date between CONVERT(DATETIME,'".$date_start."',110) and CONVERT(DATETIME,'".$date_end."',110)".$where ;
                    $dt_overtime = DB::connection('ifcapb')->select($sql);
                    $list_log = '';$i=1;
                    if(!empty($dt_overtime))
                    {
                        foreach ($dt_overtime as $overtime) {
                            $descs = '';
                            $status = $overtime->status;
                            if($status=='N'){
                                $descs = 'Activated';
                            }else if($status=='P'){
                                $descs = 'Closed';
                            }
                            $list_log .= '<tr class="odd">';
                            $list_log .= '<td style="padding: 5px">' .$i. '</td>';
                            $list_log .= '<td style="padding: 5px">' .$overtime->lot_no. '</td>';
                            $list_log .= '<td style="padding: 5px">' .$overtime->debtor_acct. '</td>';
                            $list_log .= '<td style="padding: 5px">' .date("d M Y H:i:s",strtotime($overtime->begin_date)). '</td>';
                            $list_log .= '<td style="padding: 5px">' .date("d M Y H:i:s",strtotime($overtime->end_date)). '</td>';
                            $list_log .= '<td style="padding: 5px">'. $descs. '</td>';
                            $list_log .= '<td style="padding: 5px">' .$overtime->remarks. '</td>';
                            $list_log .= '</tr>';
                            $i++;
                        }
                    }else{
                        $list_log.='<tr class="odd">';
                        $list_log.='<td colspan="7" style="text-align:center">No Data Available</td>';
                        $list_log.='</tr>';
                    }
                    $content = array('listD'=>$list_log);
                    $nf = 'history_overtime';
                    $pdf = PDF::loadView('admin.history.expOT', $content);
                    return $pdf->stream($nf.'.pdf');
                    break;
                    break;
                default:
                    abort(404);
                    break;
            }

        } else {
            abort(404);
            exit();
        }

        
    }
}
