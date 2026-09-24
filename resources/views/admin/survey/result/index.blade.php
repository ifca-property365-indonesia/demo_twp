@extends('admin.template.layout2.base')
@section('title', __('admin/survey.survey_results'))
@section('content')
<div class="page-body">
    <div>
        <div class="page-block">
            <div class="page-head">
                <div class="page-head-row">
                    <div class="page-head-content">
                        <h3 class="page-title">{{ __('admin/survey.survey_results') }}</h3>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered w-100" id="tblpublished">
                            <thead>
                                <tr>
                                    <th>{{ __('admin/survey.no') }}</th>          
                                    <th class="sorting_asc">{{ __('admin/survey.survey_title') }}</th>
                                    <th>{{ __('admin/survey.subject') }}</th>
                                    <th>{{ __('admin/survey.publish_date') }}</th>
                                    <th>{{ __('admin/survey.expired_date') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div><!-- . -->
        </div> <!-- page-block -->
    </div>
</div>
<script type="text/javascript">
    var tblsurvey;
    var tblpublishedd;
    $(function(){
         tblpublishedd = $('#tblpublished').DataTable( 
        {
             "language": {
                "decimal": ",",
                "thousands": ".",
            },
             "dom": '<"toolbar tblpublished">frtip',
            select: true,
            "serverSide": true,
            "ajax":{
                "url":"{{ url('admin/survey/result/all')}}",
                "data":{"sSearch": function(d){
                    var search = $('#txt_search').val();
                    var b="";
                    if(search == null || search==""){
                        return b;
                    }{
                        return search;
                    }
                 }},             
                "type":"POST"
            },
            "columns": [
                {data: "row_number",name:"row_number", searchable:false},
                {data: "title",name:"title", searchable:true},
                {data:"subjects",name:"subjects",searchable:true,
                     render: function (data, type, row) {
                    x=data;
                    var cc='<ul style="list-style-type:disc;margin-left: 20px">';
                    xArray = x.split(',');
                    $.each(xArray, function(index, value) { 
                        cc=cc+'<li>'+value+'</li>';
                    });
                    return cc+'</ul>';
                    }
                },
                {data: "publishdate",name:"publishdate",searchable:true,
                render: function (data, type, row) {
                    return FormatDateNew(data);
                }},
                {data: "expireddate",name:"expireddate",searchable:true,
                render: function (data, type, row) {
                    return FormatDateNew(data);
                }},
               
            ]
          
        });
        $("div.tblpublished").html(
            '<button id="btnresult" class="btn btn-sm btn-primary">{{ __('admin/survey.result') }}</button>&nbsp;'
        );
        tblpublishedd.on('click', 'tr', function() {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            } else {
                tblpublishedd.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });
        $('#btnresult').click(function(){
            var rows = tblpublishedd.rows('.selected').indexes();
            if (rows.length < 1) {
                Swal.fire(@json(__('common.information')),@json(__('admin/survey.select_row')),"warning");
                return;
            } 
            var data = tblpublishedd.rows(rows).data();
            var publish_id = data[0].publish_id;
            block(true,'#modalbodyxl');
         
            window.location.href = "{{ url('/admin/survey/result/see/') }}"+'/'+publish_id;
            
        });
    });
</script>
@endsection

