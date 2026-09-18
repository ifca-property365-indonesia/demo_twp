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
                    <h4 class="nk-block-title">Reset Password</h4>
                </div>
            </div>
            <div class="card card-preview">
                <div class="card-inner">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tblresett" width="100%">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Business Name</th>
								<th>Group Access</th>
                                <th>Email</th>
                                <th>Action</th>
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
  var tblreset;
  $(function() {
    $('.select2').select2();
    tblreset = $('#tblresett').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            "url" : "{{ url('/admin/account/data') }}",
            "type": "POST",
            data: {
              "_token": "{{ csrf_token() }}"
              }
            },
          columns: [
              { data: 'row_number', name: 'row_number' },
              { data: 'name', name: 'name' },
			  { data: 'tableforeign', name: 'tableforeign' },
              { data: 'email', name: 'email' },
              { data: 'email', name: 'email' , 
                render:function(data,type,row){
                    return '<button onclick="resetpass(\''+data+'\',\''+row.name+'\')" class="btn btn-primary"> Reset </button>';
              }},
          ],
          dom: '<"toolbar group">frtip',
          "responsive": {
            details: {
                type: 'column',
                target: 8
            }
          }
      });

      tblreset.on('click', 'tr', function() {
          if ($(this).hasClass('selected')) {
              $(this).removeClass('selected');
          } else {

            tblreset.$('tr.selected').removeClass('selected');
              $(this).addClass('selected');
          }
      });

    });
    function resetpass(email,namee) {
        block(true,'div.card');
        Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            })
            .then(function(a){
                if (a.value==true) {
                    $.ajax({
                        url : "{{ url('/admin/account/resetpass') }}",
                        type:"POST",
                        data: { email: email,name: namee,"_token": "{{ csrf_token() }}" },
                        dataType:"json",
                        success:function(event, data){
                            if (event.status == 'OK')
                          {
                              Swal.fire({
                                  title: "Information",
                                  icon:"success",
                                  text: event.pesan
                              }).then(function(a){
                                tblreset.ajax.reload(null,true);
                            });
                            
                          } else {
                              if (event.status == "warning")
                              {
                                Swal.fire({
                                      title: "Information",
                                      icon:"warning",
                                      text: event.pesan
                                  });
                              } else {
                                  Swal.fire({
                                      title: "Information",
                                      icon:"error",
                                      text: event.pesan
                                  });
                              }
                          }
                            // Swal.fire("Information",event.pesan,"success");
                            block(false,'div.card');
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            Swal.fire("Information",textStatus+' Save : '+errorThrown,"warning");
                            block(false,'div.card');
                        }
                    });
                }else{
                    block(false,'div.card');
                }
            })
        
    }

</script>

@endsection

