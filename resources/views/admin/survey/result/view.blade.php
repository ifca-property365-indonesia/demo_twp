@extends('admin.template.layout2.base')
@section('title', __('admin/survey.survey_result'))
@section('content')
<div class="page-body">
    <div>
      <div class="page-block">
        <div class="page-head">
                <div class="page-head-row">
                    <div class="page-head-content">
                        <h3 class="page-title">{{ __('admin/survey.survey_result') }} 
            </h3>
                    </div>
                </div>
            </div>
        <div class="card">
            <div class="card-body">
                
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
                        if ($e!= $r) {
                            echo '<h5 style="margin-top:10px;">'.$key->title.'</h5>
                                '.e(__('admin/survey.total_respondents_count', ['count' => $res]));
                        }
                        $r=$e;

                        $k=$key->content;
                        if ($k!= $l) {
                            echo '<br><hr><h6>'.$key->content.'</h6><hr>';
                        }
                        $l=$k;

                        $s = $key->options;

                        if($s != $q){

                            if($res != 0){
                                $JDebtor = ($key->jumlah / $res) * 100;
                            } else {
                                $JDebtor = 0;
                            }

                            $percent = number_format($JDebtor, 2);

                            echo '<table width="100%">
                                    <tr>
                                        <td width="75%">
                                            '.$key->line_no.'. '.$key->options.' <br>

                                            <div class="progress" style="margin-bottom:10px;">
                                                <div class="progress-bar progress-bar-striped" 
                                                    role="progressbar" 
                                                    style="width:'.$percent.'%">
                                                </div>
                                            </div>

                                            <strong>
        '.e(__('admin/survey.respondents_people', ['count' => $key->jumlah])).' 
        <span class="badge rounded-pill text-bg-info" style="margin-left:10px;">
            '.$percent.'%
        </span>
      </strong><br>';
                        }

                        if(!empty($key->email_addr)){
                            echo '<small>- '.$key->company_name.' ('.$key->date_created.')</small><br>';
                        }

                        if($s != (isset($dtsurvey[$no]->options) ? $dtsurvey[$no]->options : null)){

                            echo '</td>
                                </tr>
                            </table>';
                        }

                        $q = $s;
                    }
                    ?>
            </div>
        </div>
      </div>
    </div>
</div>
<script type="text/javascript">
    function goback(){
        window.location.href="{{ url('admin/survey/result') }}";
    }
    function generate(){
        var publish_id="{{ $id }}";
        var site_url = '{{ url("/admin/survey/result/export")}}'+'/'+publish_id;
        window.open(site_url);
    }
</script>
@endsection
