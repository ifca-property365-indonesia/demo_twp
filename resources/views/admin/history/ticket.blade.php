@extends('admin.template.layout2.base')
@section('title', __('admin/history.ticket_history'))

@section('content')
<div class="page-body">
    <div>
        <div class="page-block">
            <div class="page-head">
                <div class="page-head-row">
                    <div class="page-head-content">
                        <h3 class="page-title">{{ __('admin/history.ticket_history') }}</h3>
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
                            <label class="form-label" for="start">{{ __('admin/history.reported_date_from') }}</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                <input type="text" id="start" name="start" class="form-control date-picker" data-date-format="dd/mm/yyyy" data-date-end-date="0d" value="" placeholder="{{ __('common.select_date') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label" for="end">{{ __('admin/history.reported_date_to') }}</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                <input type="text" id="end" name="end" class="form-control date-picker" data-date-format="dd/mm/yyyy" data-date-end-date="0d" value="" placeholder="{{ __('common.select_date') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="debtor">{{ __('common.tenant') }}</label>
                            <select name="debtor" id="debtor" data-placeholder="{{ __('admin/history.choose_tenant') }}" class="form-control select2">
                                    <option value=""></option>
                                    <option value="all">{{ __('common.all') }}</option>
                                    <?php if(!empty($datadebtor)) {
                                        foreach($datadebtor as $key){
                                            echo "<option value='".$key->debtor_acct."'>".$key->name."</option>";
                                        }  
                                    } ?>  
                                </select>
                        </div>
                        <div class="col-sm-4 col-md-2">
                            <button type="button" class="btn btn-primary w-100" id="btnsearch"><i class="cil-search"></i><span>{{ __('common.search') }}</span></button>
                        </div>
                    </div>
<div class="table-responsive">
                        <table class="table table-hover table-bordered w-100" id="tableLatestTickett">
                            <thead>
                                <tr>
                                    <th class="sorting_asc">{{ __('admin/history.col_no') }}</th>
                                    <th>{{ __('admin/history.wo_number') }}</th>
                                    <th>{{ __('common.category') }}</th>
                                    <th>{{ __('admin/history.tenant_name') }}</th>
                                    <th>{{ __('common.description') }}</th>
                                    <th>{{ __('admin/history.reported_date') }}</th>
                                    <th>{{ __('admin/history.request_by') }}</th>
                                    <th>{{ __('admin/history.lot_number') }}</th>
                                    <th>{{ __('admin/history.ticket_status') }}</th>
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
  var STATUS_LABELS = @json(__('admin/history.ticket_statuses'));
  $(function() {
    // Tanggal (sama dengan form Letter Permit): end >= start, end kosong ikut start
    $('#start').on('change', function () {
        var start = $(this).val() ? $(this).datepicker('getDate') : null;
        var end = $('#end').val() ? $('#end').datepicker('getDate') : null;
        $('#end').datepicker('setStartDate', start || false);
        if (start && (!end || end < start)) {
            $('#end').datepicker('update', start);
        }
    });
    $('.select2').select2();
    tblticket = $('#tableLatestTickett').DataTable({
          processing: true,
          serverSide: true,              
          ajax: {
            "url" : "{{ url('/admin/history/data/ticket') }}",
            "type": "POST",
            data: {
              "_token": "{{ csrf_token() }}",
              // dd/mm/yyyy dari datepicker (diurai server lewat TicketHd::toYmd), kosong = tanpa batas
              "date_end": function(d){
                            return $('#end').val() || '';
                        },
                        "date_start": function(d){
                            return $('#start').val() || '';
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
            {data:"report_no",name:"report_no", sortable: false},
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
                        case 'O':
                            status = STATUS_LABELS.O;
                            label = "info";
                            break;
                        case 'R':
                            status = STATUS_LABELS.R;
                            label = "info";
                            break;
                        case 'A':
                            status = STATUS_LABELS.A;
                            label = "info";
                            break;
                        case 'S':
                            status = STATUS_LABELS.S;
                            label = "info";
                            break;
                        case 'P':
                            status = STATUS_LABELS.P;
                            label = "info";
                            break;
                        case 'M':
                            status = STATUS_LABELS.M;
                            label = "info";
                            break;
                        case 'Z':
                            status = STATUS_LABELS.Z;
                            label = "warning";
                            break;
                        case 'Y':
                            status = STATUS_LABELS.Y;
                            label = "success";			
                            break;		
                        case 'C':
                            status = STATUS_LABELS.C;
                            label = "success";			
                            break;
                        case 'F':
                            status = STATUS_LABELS.F;
                            label = "success";
                            break;
                        case 'X':
                            status = STATUS_LABELS.X;
                            label = "secondary";
                            break;
                        default:
                            // status di luar daftar (mis. 'O'): tampilkan kodenya
                            status = $('<div>').text(data == null || String(data).trim() === '' ? '-' : String(data).trim()).html();
                            label = "secondary";
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
            Swal.fire(@json(__('common.warning')), @json(__('admin/history.choose_end_date')), 'warning');
            return;
        }
        tblticket.ajax.reload(null,true);
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
                {type:"ticket",date_start:date_start,date_end:date_end,debtor_acct:(debtor == 'all' ? '' : debtor),"_token": "{{ csrf_token() }}" },
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
</script>

@endsection

