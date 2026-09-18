<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use PDF;

class SurveyResultController extends Controller
{
    public function getTable()
    {
        $query = DB::connection('ifcaadm')->select("SELECT @rownum := @rownum + 1 AS row_number, t.* FROM v_pm_survey_publish t, (SELECT @rownum := 0) r  where t.flag_publish=1 order by publishdate desc");
        return DataTables::of($query)->make(true);
    }
    public function viewresult($publish=''){
       
        $sql = "SELECT DISTINCT
                c.id AS publish_id,
                c.title AS title,
                a.content AS content,
                b.options AS options,
                b.flag_remark AS flag_remark,
                c.expireddate AS expireddate,
                c.publishdate AS publishdate,
                a.quest_no AS quest_no,
                b.line_no AS line_no,
                d.email_addr AS email_addr,
                e.name AS company_name,
                d.date_created AS date_created,
                (
                    SELECT COUNT(1)
                    FROM pm_survey_respon d2
                    WHERE d2.survey_id = b.survey_id
                    AND d2.respon = b.line_no
                ) AS jumlah
            FROM pm_survey_hd a
            JOIN pm_survey_dt b ON a.id = b.survey_id
            JOIN pm_survey_publish c ON a.publish_id = c.id
            LEFT JOIN pm_survey_respon d 
                   ON b.survey_id = d.survey_id
                  AND d.respon = b.line_no
            LEFT JOIN all_login e 
                   ON d.email_addr = e.email
            Where a.publish_id ='".$publish."'
            ORDER BY c.id, a.quest_no, b.line_no, d.date_created";
        $result1 = DB::connection('ifcaadm')->select($sql);

        $sqlLine = "SELECT (SELECT COUNT(publish_id) FROM pm_survey_respon i WHERE i.publish_id = j.id) AS cnt FROM pm_survey_publish j where id = '".$publish."'" ;
        $result3 = DB::connection('ifcaadm')->select($sqlLine);

        $content = array(
                         'dtsurvey'=>$result1,
                         'id'=>$publish,
                         'Responden'=>$result3
                     );
        return view('admin.survey.result.view',$content);
    }
    function generatepdf(Request $request)
    {
        $publish = $request->id;
        $sql = "SELECT * FROM v_pm_survey_result where publish_id ='".$publish."' ORDER BY publish_id ASC" ;
        $result1 = DB::connection('ifcaadm')->select($sql);

        $sqlLine = "SELECT (SELECT COUNT(DISTINCT user_id) FROM pm_survey_respon i WHERE i.publish_id = j.id) AS cnt FROM pm_survey_publish j where id = '".$publish."'" ;
        $result3 = DB::connection('ifcaadm')->select($sqlLine);
        if(!empty($result1)){
            $content = array(
                'dtsurvey'=>$result1,
                'Responden'=>$result3
            );
            $pdfname="survey_result";
            $pdf = PDF::loadView('admin.survey.result.pdfview', $content);
            return $pdf->stream($pdfname.'.pdf');
        }else{
            return abort(404);
        }
    }
}
