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
                    <h4 class="nk-block-title">Ticket History</h4>
                </div>
            </div>
            <div class="card card-preview">
                <div class="card-inner">
                    <div class="form-group">
                        <div  style="display: flex">
                            <label for="pl_project" class="form-label col-2" style="padding-right:20px;"> Reported Date</label>
                            
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left">
                                    <em class="icon ni ni-calendar"></em>
                                </div>
                                <input type="text" id="start" name="start" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="" width="50%">
                            </div>
                            <span class="badge-sm badge-gray badge-dim" style="font-size: 15px"> to </span>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left">
                                    <em class="icon ni ni-calendar"></em>
                                </div>
                                <input type="text" id="end" name="end" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="" width="50%">
                            </div>
                            
                        </div>
                    </div>
                    <div class="form-group">
                            <div  style="display: flex">
                                <label class="form-label col-2" for="default-01">Tenant</label>
                                <div class="col-6" style="padding:0px"><select name="debtor" id="debtor" data-placeholder="Choose Tenant" class="form-control select2" tabindex="2" width="50%">
                                    <option value=""></option>
                                    <option value="all">All</option>
                                    <?php if(!empty($datadebtor)) {
                                        foreach($datadebtor as $key){
                                            echo "<option value='".$key->debtor_acct."'>".$key->name."</option>";
                                        }  
                                    } ?>  
                                </select></div><button class="btn btn-primary btn-sm" id="btnsearch" style="margin-left: 15px"><em class="icon ni ni-search"></em>Search</button>
                            </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableLatestTickett" width="100%">
                            <thead>
                                <tr>
                                    <th class="sorting_asc">No.</th>
                                    <th>Ticket Number</th>
                                    <th>Category</th>
                                    <th>Tenant Name</th>
                                    <th>Description</th>
                                    <th>Reported Date</th>
                                    <th>Request By</th>
                                    <th>Lot Number</th>
                                    <th>Ticket Status</th>
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
  var tblticket;
  $('.date-picker').datepicker('setEndDate', new Date());
  $(function() {
    $('.select2').select2();
    tblticket = $('#tableLatestTickett').DataTable({
          processing: true,
          serverSide: true,              
          ajax: {
            "url" : "{{ url('/admin/history/data/ticket') }}",
            "type": "POST",
            data: {
              "_token": "{{ csrf_token() }}",
              "date_end": function(d){
                            var a = $('#end').val();

                            var date = new Date(parseInt(a.substr(0,10)));
                            var year =a.substr(6,4);
                            var month=a.substr(3,2);
                            var day =a.substr(0,2);
                                       
                            var aa1 = year+"/"+month+"/"+day;
                            var b ="";
                            if(aa1 == "//"){
                                return b;
                            }{
                                return aa1;
                            }
                       
                        },
                        "date_start": function(d){
                            var a = $('#start').val();
                            var date = new Date(parseInt(a.substr(0,10)));
                            var year =a.substr(6,4);
                            var month=a.substr(3,2);
                            var day =a.substr(0,2);
                                       
                            var aa1 = year+"/"+month+"/"+day;
                            var b ="";
                            if(aa1 == "//"){
                                return b;
                            }{
                                return aa1;
                            }
                        },
                        "debtor_acct": function (d) {
                            var search = $('#debtor').val();
                            var b = "";
                            if (search == null || search == "" || search == "all") {
                                return b;
                            } {
                                return search;
                            }
                        }
              }
            },
          columns: [
            {data: "row_number",name:"row_number", searchable:false},
            {data:"complain_no",name:"complain_no", sortable: false},
            {data:"categoryname",name:"categoryname"},
            {data:"name",name:"name"},
            {data:"work_requested",name:"work_requested"},
            {data:"reported_date",name:"reported_date",
                render:function (data,type,row) {
                    return FormatDateNew(data); 
                }
            },
            {data:"serv_req_by",name:"serv_req_by", sortable: true},
            {data:"lot_no",name:"lot_no", sortable: true},
            {data:"status",name:"status",
                render:function (data,type,row) {
                    var label,status;
                    switch (data) {
                        case 'R':
                            status = "Open";
                            label = "info";
                            break;
                        case 'A':
                            status = "Accepted";
                            label = "info";
                            break;
                        case 'S':
                            status = "Survey";
                            label = "info";
                            break;
                        case 'P':
                            status = "Process";
                            label = "info";
                            break;
                        case 'M':
                            status = "Modify";
                            label = "info";
                            break;
                        case 'Z':
                            status = "Charged Approved";
                            label = "warning";
                            break;
                        case 'Y':
                            status = "Approve";
                            label = "success";			
                            break;		
                        case 'C':
                            status = "Close";
                            label = "success";			
                            break;
                        case 'F':
                            status = "Confirm";
                            label = "success";
                            break;
                        case 'X':
                            status = "Cancel";
                            label = "default";			
                            break;
                    }
                    return '<span class="badge badge-'+label+'"> '+status+' </span>';
             
                }}
          ],
          dom: '<"toolbar group">frtip',
          responsive: false,
          columnDefs: [
            { responsivePriority: 1, targets: 8 } // Ticket Status jangan disembunyikan
        ]
      });
   
    });

    $('#btnsearch').click(function(){
        var date_end = $('#end').val();
        var date_start = $('#start').val();

        if (date_start!='' && date_end=='')
        {
            swal('Warning','Please choose end date','warning');
            return;
        }
        tblticket.ajax.reload(null,true);
    });
    $('#btngenpdf').click(function(){
        var date_end = $('#end').val();
        var date_start = $('#start').val();

        if (date_start!='' && date_end=='')
        {
            swal('Warning','Please choose end date','warning');
            return;
        }
        var debtor = $('#debtor').val();
        var site_url = '{{ url("admin/history/dlpdf")}}';
            $.post(site_url,
                {type:"ticket",date_start:date_start,date_end:date_end,debtor:debtor,"_token": "{{ csrf_token() }}" },
                function(data,status) {
                    console.log(data,status);
                    if(status=='success'){
                        window.open(data);
                    }else{
                        Swal.fire({
                                    title: "Information",
                                    icon:"error",
                                    text: "Failed generating pdf file."
                                });
                    }
            });
    
      });
</script>

@endsection

