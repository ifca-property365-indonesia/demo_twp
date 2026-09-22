@extends('admin.template.layout2.base')
@section('title', 'Ticket History')

@section('content')
<div class="page-body">
    <div>
        <div class="page-block">
            <div class="page-head">
                <div class="page-head-row">
                    <div class="page-head-content">
                        <h3 class="page-title">Ticket History</h3>
                    </div>
                    <div class="page-head-content">
                        <button type="button" class="btn btn-outline-secondary" id="btngenpdf"><i class="cil-cloud-download"></i><span>Generate PDF</span></button>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label" for="start">Reported Date From</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                <input type="text" id="start" name="start" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="" placeholder="dd/mm/yyyy" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label" for="end">To</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                <input type="text" id="end" name="end" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="" placeholder="dd/mm/yyyy" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="debtor">Tenant</label>
                            <select name="debtor" id="debtor" data-placeholder="Choose Tenant" class="form-control select2">
                                    <option value=""></option>
                                    <option value="all">All</option>
                                    <?php if(!empty($datadebtor)) {
                                        foreach($datadebtor as $key){
                                            echo "<option value='".$key->debtor_acct."'>".$key->name."</option>";
                                        }  
                                    } ?>  
                                </select>
                        </div>
                        <div class="col-sm-4 col-md-2">
                            <button type="button" class="btn btn-primary w-100" id="btnsearch"><i class="cil-search"></i><span>Search</span></button>
                        </div>
                    </div>
<div class="table-responsive">
                        <table class="table table-hover table-bordered w-100" id="tableLatestTickett">
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
            </div><!-- . -->
        </div> <!-- page-block -->
    </div>
</div>

<script type="text/javascript">
  var tblticket;
  $(function() {
    $('.date-picker').datepicker('setEndDate', new Date());
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
                            label = "secondary";			
                            break;
                    }
                    return '<span class="badge badge-soft-'+label+'">'+status+'</span>';
             
                }}
          ],
      });
   
    });

    $('#btnsearch').click(function(){
        var date_end = $('#end').val();
        var date_start = $('#start').val();

        if (date_start!='' && date_end=='')
        {
            Swal.fire('Warning', 'Please choose end date', 'warning');
            return;
        }
        tblticket.ajax.reload(null,true);
    });
    $('#btngenpdf').click(function(){
        var date_end = $('#end').val();
        var date_start = $('#start').val();

        if (date_start!='' && date_end=='')
        {
            Swal.fire('Warning', 'Please choose end date', 'warning');
            return;
        }
        var debtor = $('#debtor').val();
        var site_url = '{{ url("admin/history/dlpdf")}}';
            $.post(site_url,
                {type:"ticket",date_start:date_start,date_end:date_end,debtor_acct:(debtor == 'all' ? '' : debtor),"_token": "{{ csrf_token() }}" },
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

