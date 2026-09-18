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
                    <h4 class="nk-block-title">Overtime History</h4>
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
                                            echo "<option value='".$key->debtor_acct."'>".$key->debtor_acct."</option>";
                                        }  
                                    } ?>  
                                </select></div><button class="btn btn-primary btn-sm" id="btnsearch" style="margin-left: 15px"><em class="icon ni ni-search"></em>Search</button>
                            </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tblovertimee" width="100%">
                            <thead>
                                <tr>
                                    <th class="sorting_asc">No.</th>
                                    <th>Lot Number</th>
                                    <th>Tenant</th>
                                    <th>Start Overtime</th>
                                    <th>End Overtime</th>
                                    <th>Status</th>
                                    <th>Description</th>
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
  var tblovertime;
  $(function() {
    $('.select2').select2();
    tblovertime = $('#tblovertimee').DataTable({
          processing: true,
          serverSide: true,              
          ajax: {
            "url" : "{{ url('/admin/history/data/overtime') }}",
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
                        }//,
          
              }
            },
          columns: [
            {data: "row_number",name:"row_number", searchable:false},
            {data:"lot_no",name:"lot_no", sortable: false},
            {data:"debtor_acct",name:"debtor_acct"},
            {data:"begin_date",name:"begin_date",
                render:function (data,type,row) {
                    return FormatDateTimeNew(data); 
                }
            },
            {data:"end_date",name:"end_date",
                render:function (data,type,row) {
                    return FormatDateTimeNew(data); 
                }
            },
            {data:"status",name:"status",
                render:function (data,type,row) {
                   if(data=='N'){
                        return '<span class="badge badge-success"> Activated </span>';
                   } else if(data=='P'){
                        return '<span class="badge badge-danger"> Closed </span>';
                   } else {
                        return '';
                   }
                }
            },
            {data:"remarks",name:"remarks"}
          ],
          dom: '<"toolbar group">frtip',
          "responsive": {
            details: {
                type: 'column',
                target: 8
            }
          }
      });

    $('#btnsearch').click(function(){
        var date_end = $('#end').val();
        var date_start = $('#start').val();

        if (date_start!='' && date_end=='')
        {
            swal('Warning','Please choose end date','warning');
            return;
        }
        tblovertime.ajax.reload(null,true);
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
                {type:"overtime",date_start:date_start,date_end:date_end,debtor:debtor,"_token": "{{ csrf_token() }}" },
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
    });
    
   
</script>

@endsection

