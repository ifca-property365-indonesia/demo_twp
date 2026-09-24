@extends('admin.template.layout2.base')
@section('title', __('admin/history.overtime_history'))

@section('content')
<div class="page-body">
    <div>
        <div class="page-block">
            <div class="page-head">
                <div class="page-head-row">
                    <div class="page-head-content">
                        <h3 class="page-title">{{ __('admin/history.overtime_history') }}</h3>
                    </div>
                    <div class="page-head-content">
                        <button type="button" class="btn btn-pdf" id="btngenpdf"><i class="cil-cloud-download"></i><span>{{ __('common.generate_pdf') }}</span></button>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label" for="start">{{ __('admin/history.request_date_from') }}</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                <input type="text" id="start" name="start" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="" placeholder="{{ __('common.select_date') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label" for="end">{{ __('admin/history.reported_date_to') }}</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                <input type="text" id="end" name="end" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="" placeholder="{{ __('common.select_date') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="debtor">{{ __('common.tenant') }}</label>
                            <select name="debtor" id="debtor" data-placeholder="{{ __('admin/history.choose_tenant') }}" class="form-control select2">
                                    <option value=""></option>
                                    <option value="all">{{ __('common.all') }}</option>
                                    <?php if(!empty($datadebtor)) {
                                        foreach($datadebtor as $key){
                                            echo "<option value='".$key->debtor_acct."'>".$key->debtor_acct."</option>";
                                        }  
                                    } ?>  
                                </select>
                        </div>
                        <div class="col-sm-4 col-md-2">
                            <button type="button" class="btn btn-primary w-100" id="btnsearch"><i class="cil-search"></i><span>{{ __('common.search') }}</span></button>
                        </div>
                    </div>
<div class="table-responsive">
                        <table class="table table-hover table-bordered w-100" id="tblovertimee">
                            <thead>
                                <tr>
                                    <th class="sorting_asc">{{ __('admin/history.col_no') }}</th>
                                    <th>{{ __('admin/history.lot_number') }}</th>
                                    <th>{{ __('common.tenant') }}</th>
                                    <th>{{ __('admin/history.start_overtime') }}</th>
                                    <th>{{ __('admin/history.end_overtime') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th>{{ __('common.description') }}</th>
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
                        return '<span class="badge text-bg-success"> ' + @json(__('admin/history.activated')) + ' </span>';
                   } else if(data=='P'){
                        return '<span class="badge text-bg-danger"> ' + @json(__('admin/history.closed')) + ' </span>';
                   } else {
                        return '';
                   }
                }
            },
            {data:"remarks",name:"remarks"}
          ]
      });

    $('#btnsearch').click(function(){
        var date_end = $('#end').val();
        var date_start = $('#start').val();

        if (date_start!='' && date_end=='')
        {
            Swal.fire(@json(__('common.warning')), @json(__('admin/history.choose_end_date')), 'warning');
            return;
        }
        tblovertime.ajax.reload(null,true);
    });
    $('#btngenpdf').click(function(){
        var date_end = $('#end').val();
        var date_start = $('#start').val();

        if (date_start!='' && date_end=='')
        {
            Swal.fire(@json(__('common.warning')), @json(__('admin/history.choose_end_date')), 'warning');
            return;
        }
        var debtor = $('#debtor').val();
        var site_url = '{{ url("admin/history/dlpdf")}}';
            $.post(site_url,
                {type:"overtime",date_start:date_start,date_end:date_end,debtor_acct:(debtor == 'all' ? '' : debtor),"_token": "{{ csrf_token() }}" },
                function(data,status) {
                    console.log(data,status);
                    if(status=='success'){
                        window.open(data);
                    }else{
                        Swal.fire({
                                    title: @json(__('common.information')),
                                    icon:"error",
                                    text: @json(__('admin/history.pdf_failed'))
                                });
                    }
            });
    
      });
    });
    
   
</script>

@endsection

