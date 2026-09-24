<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>IFCA Software</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="{{ url('assets/pdf/bootstrap.min.css');}}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/pdf/skin-yellow.min.css'); }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/pdf/AdminLTE.min.css'); }}" rel="stylesheet" type="text/css" />
    <style type="text/css">.boxx{position: relative;width: 16px;height: 16px;border-radius: 3px;}.footer{position: fixed; bottom: 0px; text-align: center;}thead:before, thead:after { display: none; }tbody:before, tbody:after { display: none; }table tr td { page-break-inside: avoid!important; }table.print-friendly tbody tr td, table.print-friendly tbody tr th {page-break-inside: avoid;} h4,h5{font-family: Arial, Helvetica, sans-serif!important}</style>
  </head>
  <body style="font-family: Arial, Helvetica, sans-serif!important">
    <section class="invoice">
      <p style="font-size:18px;font-weight:bold">Survey Result</p><hr/>
      <div class="row">
      <div class="col-sm-12">
        <div class="box" style="border-top: none;"> 
        <?php 
            $jumlah[]='';
            $s='';$q='';$k='';$l='';$e='';$r='';
            $no=0;
            if ($Responden[0]->cnt == 0) {
                $res = 0;
            } else {
                $res = $Responden[0]->cnt;
            }

            $JDebtor = 0;
            foreach ($dtsurvey as $key) {
                    
                $e=$key->title;
                $o1= '';
                if ($e!= $r) {
                    echo '<p style=" margin-top: 10px;font-size:18px;font-weight:bold">'. $o1 = $key->title.'</p>Total Respondents : '.$res;
                }
                $r=$e;
                
                $k=$key->content;
                $z= '';
                if ($k!= $l) {
                    echo '<br><hr><p style="font-size:14px;font-weight:bold">'. $z = $key->content.'</p><hr>';
                }
                $l=$k;
                $s = $key->options;

                if($s!= $q){
                    if($res!=0){
                        $JDebtor = ($key->jumlah/$res)*100;
                    }else{
                        $JDebtor = 0;
                    }
                    
                    $no++;
                    echo '<table width="100%" class="print-friendly">
                            <tr>
                                <td width="75%">'. $key->line_no;
                                    echo '. '.$key->options.'
                                    <div class="progress progress-lg">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="'.(int)$JDebtor.'" aria-valuemin="'.(int)$JDebtor.'" aria-valuemax="100"
                                        style="width:'.(int)$JDebtor.'%"></div>
                                    </div>
                                </td>
                                <td width="25%"><span class="badge badge-pill" style="margin-left:15px;color: #fff;background-color: #17a2b8;">'.(int)$JDebtor.'%</span></td>
                            </tr></table>';
                }
                    
                $q = $s;
            } ?>
        </div>  
    </div>
      </div>
    <div class="footer">
        <p style="font-size:8px">WINDAS Tenant Web Portal may contain information that is created and managed by various sources, both internal and external. At no time shall WINDAS Building Management be responsible or liable, directly or indirectly, for any damage or loss resulting from or alleged to result from the use of or reliance on any such content in WINDAS Tenant Web Portal</p>
    </div>
    </section>
  </body>
</html>
