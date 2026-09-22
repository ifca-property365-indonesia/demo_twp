@extends('admin.template.layout2.base')
@section('title', 'Reset Password')

@section('content')
<div class="page-body">
    <div class="mx-auto" style="max-width: 1100px;">
        <div class="page-block">
            <div class="page-head">
                <div class="page-head-row">
                    <div class="page-head-content">
                        <h3 class="page-title">Reset Password</h3>
                        <div class="page-desc">Reset a user password to the default password.</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered w-100" id="tblresett">
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
            </div><!-- . -->
        </div> <!-- page-block -->
    </div>
</div>

<script type="text/javascript">
  var tblreset;
  $(function() {
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
                    return '<button onclick="resetpass(\''+data+'\',\''+row.name+'\')" class="btn btn-sm btn-primary"><i class="cil-reload"></i> Reset</button>';
              }},
          ],
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

