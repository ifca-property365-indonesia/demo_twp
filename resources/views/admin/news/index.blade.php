@extends('admin.template.layout2.base')
@section('content')
<style type="text/css">
    .toolbar {
        float: left;
        margin-bottom: 1em;
    }
</style>
<div class="nk-content-body">
    <div class="components-preview wide-md mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title">News and Promo</h4>
                </div>
            </div>
            <div class="card card-preview">
                <div class="card-inner">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tblgroup" width="100%">
                            <thead>
                            <tr>
                                <th style="padding-right: 20px;padding-left: 10px;">No</th>
                                <th>Content Type</th>
                                <th width="45%">Title</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div><!-- .card-preview -->
        </div> <!-- nk-block -->
    </div>
</div>

<script type="text/javascript">
    var tblgroupp;
    $(function() {
        tblgroupp = $('#tblgroup').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
            "url" : "{{ url('/admin/news/all') }}",
            "type": "POST",
            data: {
                "_token": "{{ csrf_token() }}"
            }
        },
        columns: [
            { data: 'row_number', name: 'row_number' },
            { data:"content_type", name:"content_type", sortable: false},
            { data:"subject",name:"subject"},
            {
                data: "start_date",
                name: "start_date",
                sortable: true,
                render: function(data, type, row) {
                    if (!data) return '';

                    const date = new Date(data);

                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();

                    return `${day}-${month}-${year}`;
                }
            },
            {
                data: "end_date",
                name: "end_date",
                sortable: true,
                render: function(data, type, row) {
                    if (!data) return '';

                    const date = new Date(data);

                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();

                    return `${day}-${month}-${year}`;
                }
            },
            {
                data: "end_date",
                name: "status",
                orderable: false,
                searchable: false,
                render: function(data) {
                    if (!data) return '';

                    const endDate = new Date(data);
                    endDate.setHours(23,59,59,999);

                    const today = new Date();

                    if (today > endDate) {
                        return '<span class="badge badge-danger">Expired</span>';
                    }

                    return '<span class="badge badge-success">Active</span>';
                }
            }
          ],
          dom: '<"toolbar group">frtip',
          "responsive": {
            details: {
                type: 'column',
                target: 8
            }
          }
      });
      $("div.group").html(
        '<button id="addgroup" class="btn btn-primary pull-up" style="margin-top: 5px">Add</button>&nbsp;'+
        '<button id="editgroup" class="btn btn-info pull-up" style="margin-top: 5px">Edit</button>&nbsp;'+
        '<button id="deletegroup" class="btn btn-danger pull-up" style="margin-top: 5px">Delete</button>&nbsp;'

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
        window.location.href="{{url('/admin/news/form/A')}}";

      })

      $('#editgroup').click(function(){
        var rows = tblgroupp.rows('.selected').indexes();
        if (rows.length < 1) {
            Swal.fire("Information",'Please select a row',"warning");
            return;
        }
        var data = tblgroupp.rows(rows).data();
        var id = data[0].id;
        var site_url = "{{url('/admin/news/form')}}"+'/E/'+id;

        window.location.href=site_url;

    })

        $('#deletegroup').click(function(){
            var rows = tblgroupp.rows('.selected').indexes();
            if (rows.length < 1) {
                Swal.fire("Information",'Please select a row',"warning");
                return;
            }
            var data = tblgroupp.rows(rows).data();
            var id = data[0].id;

            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then(function(a){
                if (a.value==true) {
                    Delete(id);
                }else{
                }
            })
        })
    });

    function Delete(id) {
        $.ajax({
            url : "{{ url('/admin/news/delete') }}",
            type:"POST",
            data: { id: id,"_token": "{{ csrf_token() }}" },
            dataType:"json",
            success:function(event, data){
                Swal.fire("Information",event.pesan,"success");
                tblgroupp.ajax.reload(null,true);
            },
            error: function(jqXHR, textStatus, errorThrown){
                Swal.fire("Information",textStatus+' Save : '+errorThrown,"warning");
            }
        });
    }

</script>

@endsection

