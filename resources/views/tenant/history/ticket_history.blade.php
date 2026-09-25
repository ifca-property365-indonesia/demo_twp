@extends('tenant.template.base')
@section('title', __('tenant/history.ticket_title'))
@section('content')
	<div class="page-body">
        <div class="page-head">
            <div class="page-head-row">
                <div class="page-head-content">
                    <h3 class="page-title">{{ __('tenant/history.ticket_title') }}</h3>
                </div><!-- .page-head-content -->
            </div><!-- .page-head-row -->
        </div><!-- .page-head -->
        <div class="page-block">
        	<div class="card mb-3">
            <div class="card-body">
                <form id="form_search" class="form-validate" method="POST" action="" novalidate>
                @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-sm-6 col-lg-3">
                            <label for="start" class="form-label">{{ __('common.start_date') }}</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                <input type="text" id="start" name="start" class="form-control date-picker" data-date-format="dd/mm/yyyy" data-date-end-date="0d" value="" placeholder="{{ __('common.select_date') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <label for="end" class="form-label">{{ __('common.end_date') }}</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                <input type="text" id="end" name="end" class="form-control date-picker" data-date-format="dd/mm/yyyy" data-date-end-date="0d" value="" placeholder="{{ __('common.select_date') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-4 col-lg-2">
                            <button type="submit" id="search" class="btn btn-primary w-100"><i class="cil-search"></i><span>{{ __('common.search') }}</span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

	                <div class="card">
	                	<div class="card-body">
	                		<div class="table-responsive">
	                			<table id="tblTicket" class="table table-bordered table-striped" role="grid" aria-describedby="tblTicket_info">
		                            <thead class="table-dark">
		                                <tr role="row">
		                                    <th class="sorting text-center" style="width: 7px; vertical-align: middle;">{{ __('tenant/history.col_no') }}</th>
		                                    <th class="sorting text-center" style="width: 24px;">{{ __('tenant/history.wo_number') }}</th>
		                                    <th class="sorting text-center" style="width: 24px; vertical-align: middle;">{{ __('tenant/history.category') }}</th>
		                                    <th class="sorting text-center" style="vertical-align: middle;">{{ __('tenant/history.description') }}</th>
		                                    <th class="sorting text-center" style="width: 100px; vertical-align: middle;">{{ __('tenant/history.reported_date') }}</th>
		                                    <th class="sorting text-center" style="width: 24px;">{{ __('tenant/history.request_by') }}</th>
		                                    <th class="sorting text-center" style="width: 80px; vertical-align: middle;">{{ __('tenant/history.lot_no') }}</th>
		                                    <th class="sorting text-center" style="width: 10px;">{{ __('tenant/history.ticket_status') }}</th>
		                                    <th class="text-center no-export" style="width: 10px;">{{ __('common.action') }}</th>
		                                </tr>
		                            </thead>
		                            <tbody>

		                            </tbody>
		                        </table>
	                		</div>
	                	</div>
	                </div>
        </div>
    </div>

    <script type="text/javascript">
    	// 'O' (Open) dipakai sistem IFCA untuk ticket, tidak ada di common.statuses
    	var STATUS_LABELS = @json(__('common.statuses') + ['O' => __('tenant/history.ticket_status_open')]);
    	$(document).ready(function(){
			// Tanggal (sama dengan form Letter Permit): end >= start, end kosong ikut start
			$('#start').on('change', function () {
				var start = $(this).val() ? $(this).datepicker('getDate') : null;
				var end = $('#end').val() ? $('#end').datepicker('getDate') : null;
				$('#end').datepicker('setStartDate', start || false);
				if (start && (!end || end < start)) {
					$('#end').datepicker('update', start);
				}
			});
	    	$('#tblTicket').DataTable({
	    		processing: true,
	    		serverSide: true,
	    		paging: false,
	    		responsive: true,
	    		ajax : {
		            url : "{{ url('/tenant/hticketTable') }}",
		            data: function(data) {
		                data.date_start = $("#start").val();
		                data.date_end = $("#end").val();
		            },
			        dataSrc: function (json) {
			            return json.data;
			        },
		        },
		        columns: [
		            {data:null,
		            	render: function (data, type, row, meta) {
			                return meta.row + meta.settings._iDisplayStart + 1 +'.';
			            }
		            },
		            {data:"report_no"},
		            {data:"category_desc"},
		            {data:"work_requested"},
		            {data:"reported_date",
		            	render: function (data, type, row, meta) {
			                return data ? moment(data).format('DD MMMM YYYY') : '-';
			            }
		        	},
		            {data:"serv_req_by"},
		            {data:"lot_no", defaultContent: '-'},
		            {data:"status",
		                render: function (data, type, row) {
		                    // Status yang tidak ada di daftar (mis. 'O' dari sistem lain) tampil
		                    // sebagai kodenya. Dulu status seperti itu membuat render error
		                    // (variabel color tidak terisi) dan tabel macet di "Loading".
		                    var colors = {
		                        A: 'badge-soft-primary',
		                        S: 'badge-soft-primary',
		                        P: 'badge-soft-primary',
		                        F: 'badge-soft-primary',
		                        M: 'badge-soft-primary',
		                        Z: 'badge-soft-warning',
		                        Y: 'badge-soft-success',
		                        C: 'badge-soft-success',
		                        R: 'badge-soft-info',
		                        O: 'badge-soft-info',
		                        X: 'badge-soft-secondary'
		                    };
		                    var code = data == null ? '' : String(data).trim();
		                    var known = Object.prototype.hasOwnProperty.call(colors, code) && Object.prototype.hasOwnProperty.call(STATUS_LABELS, code);
		                    var item = known ? [STATUS_LABELS[code], colors[code]] : [code || '-', 'badge-soft-secondary'];
		                    return '<span class="badge ' + item[1] + '">' + $('<div>').text(item[0]).html() + '</span>';
		                }
		            },
		            // Aksi: tombol lihat gambar ticket (kalau ada)
		            {data:"picture_url", orderable: false, searchable: false, className: 'text-center',
		                render: function (data, type) { return type === 'display' ? ticketPictureButton(data) : ''; }
		            },
		        ],
		        dom : "Bfrtip",
		        buttons: [
		            {
		                extend: 'pdf',
		                title: @json(__('tenant/history.ticket_title')),
		                className: 'btn btn-primary mb-2',
                        text: '<i class="cil-cloud-download"></i>&nbsp;' + @json(__('common.generate_pdf')),
                        init: function(api, node, config) {
                            $(node).removeClass('dt-button')
                        },
		            },
		        ]
	    	});

	    	$('#search').click(function(event){
		        event.preventDefault();
		        if (event.handled !== true)
		        {
		            event.handled = true;
		            if($('#form_search').valid())
		            {
		                var datafrm = $('#form_search').serializeArray();
		                $.ajax({
		                    url : "{{url('/tenant/hticketSearch')}}",
		                    type:"POST",
		                    data: datafrm,
		                    dataType:"json",
		                    success:function(event, data)
		                    {
		                    	console.log(data);
		                        if(event.Error==false)
		                        {
		                            $('#tblTicket').DataTable().ajax.reload();
		                        }
		                        else {
		                            Swal.fire({
		                                title: event.Pesan,
		                                // text: event.Pesan,
		                                icon:"warning",
		                                confirmButtonText: @json(__('common.ok'))
		                            });
		                            $('#tblTicket').DataTable().ajax.reload();
		                        }
		                    },
		                    error: function(jqXHR, textStatus, errorThrown){
		                        Swal.fire({
		                            title: @json(__('common.error')),
		                            animation: false,
		                            icon:"error",
		                            text: @json(__('tenant/history.search_error')).replace(':status', function () { return textStatus; }).replace(':error', function () { return errorThrown; }),
		                            confirmButtonText: @json(__('common.ok'))
		                        });
		                    }
		                });
		            }
		        }
		    });
	    });
    </script>
@endsection