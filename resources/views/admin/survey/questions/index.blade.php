@extends('admin.template.layout2.base')
@section('content')
<div class="page-body">
    <div>
        <div class="page-block">
            <div class="page-head">
                <div class="page-head-row">
                    <div class="page-head-content">
                        <h3 class="page-title">{{ __('admin/survey.question_template_entry') }}</h3>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered w-100" id="tblgroup">
                            <thead>
                            <tr>
                                <th>{{ __('admin/survey.no') }}</th>
                                <th>{{ __('admin/survey.subject') }}</th>
                                <th>{{ __('admin/survey.question') }}</th>
                                <th>{{ __('admin/survey.optional_answers') }}</th>
                                <th>{{ __('admin/survey.date_created') }}</th>
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
  var tblgroupp;
  $(function() {
    $('.select2').select2();
    tblgroupp = $('#tblgroup').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            "url" : "{{ url('/admin/survey/questions/all') }}",
            "type": "POST",
            data: {
              "_token": "{{ csrf_token() }}"
              }
            },
          columns: [
            {data: "row_number",name:"row_number", searchable:false},
            {data: "subject",name:"subject", searchable:true},
            {data: "content",name:"content", searchable:true},
            {data: "options",name:"options", searchable:false,sortable:false,
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
            {data:"date_created",name:"date_created",searchable:true,
                render: function (data, type, row) {
                    return FormatDateNew(data);
                }
            },
            {data: "tmpsurvey_id",name:"tmpsurvey_id", visible:false},
          ],
          dom: '<"toolbar group">frtip'
      });
      $("div.group").html(
        '<button id="addgroup" class="btn btn-sm btn-primary">{{ __('common.add') }}</button>&nbsp;'+
        '<button id="editgroup" class="btn btn-sm btn-info">{{ __('common.edit') }}</button>&nbsp;'+
        '<button id="deletegroup" class="btn btn-sm btn-danger">{{ __('common.delete') }}</button>&nbsp;'

      );
      tblgroupp.on('click', 'tr', function() {
          if ($(this).hasClass('selected')) {
              $(this).removeClass('selected');
          } else {

            tblgroupp.$('tr.selected').removeClass('selected');
              $(this).addClass('selected');
          }
      });

      $('#addgroup').click(function(){
        $('#modaltitlexl').addClass('white');
        $('#modaltitlexl').html(@json(__('admin/survey.question_template_entry')));
        $('#modalbodyxl').load("{{ url('/admin/survey/questions/form') }}");
        $('#modalxl').data('id', 0);
        $('#modalxl').data('form', 'add');
        $('#modalxl').modal('show');

      })

      $('#editgroup').click(function(){
        var rows = tblgroupp.rows('.selected').indexes();
        if (rows.length < 1) {
            Swal.fire(@json(__('common.information')),@json(__('admin/survey.select_row')),"warning");
            return;
        }
        var data = tblgroupp.rows(rows).data();
        var rowid = data[0].tmpsurvey_id;

        $('#modaltitlexl').addClass('white');
        $('#modaltitlexl').html(@json(__('admin/survey.question_template_edit')));
        $('#modalbodyxl').load("{{ url('/admin/survey/questions/form') }}");
        $('#modalxl').data('id', rowid);
        $('#modalxl').data('form', 'edit');
        $('#modalxl').modal('show');
    })

        $('#deletegroup').click(function(){
            var rows = tblgroupp.rows('.selected').indexes();
            if (rows.length < 1) {
                Swal.fire(@json(__('common.information')),@json(__('admin/survey.select_row')),"warning");
                return;
            }
            var data = tblgroupp.rows(rows).data();
            var id = data[0].tmpsurvey_id;
            block(true,'.page-body');
            Swal.fire({
                title: @json(__('common.are_you_sure')),
                text: @json(__('admin/survey.revert_warning')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: @json(__('admin/survey.yes_delete'))
            })
            .then(function(a){
                if (a.value==true) {
                    Delete(id);
                    
                }else{
                    block(false,'.page-body');
                }
            })
        })
    });

    function Delete(id) {
        $.ajax({
            url : "{{ url('/admin/survey/questions/delete') }}",
            type:"POST",
            data: { id: id,"_token": "{{ csrf_token() }}" },
            dataType:"json",
            success:function(event, data){
                Swal.fire(@json(__('common.information')),event.pesan,"success");
                tblgroupp.ajax.reload(null,true);
                block(false,'.page-body');
            },
            error: function(jqXHR, textStatus, errorThrown){
                Swal.fire(@json(__('common.information')),@json(__('admin/survey.status_delete_error')).replace(':status', textStatus).replace(':error', errorThrown),"warning");
                block(false,'.page-body');
            }
        });
    }

</script>

@endsection

