@extends('tenant.template.base')
@section('content')
	<div class="page-body">
        <div class="page-head">
            <div class="page-head-row">
                <div class="page-head-content">
                    <h3 class="page-title">{{ __('tenant/history.overtime_title') }}</h3>
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
                                <input type="text" id="start" name="start" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="{{ date('d/m/Y') }}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <label for="end" class="form-label">{{ __('common.end_date') }}</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                <input type="text" id="end" name="end" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="{{ date('d/m/Y') }}" required autocomplete="off">
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
	                			<table id="tblOvertime" class="table table-bordered table-striped" role="grid" aria-describedby="tblOvertime_info">
		                            <thead class="table-dark">
		                                <tr role="row">
		                                    <th class="sorting_asc text-center" style="width: 40px; vertical-align: middle;">{{ __('tenant/history.col_no') }}</th>
		                                    <th class="sorting text-center" style="width: 80px; vertical-align: middle;">{{ __('tenant/history.id') }}</th>
		                                    <th class="sorting text-center" style="width: 152px;">{{ __('tenant/history.request_date') }}</th>
		                                    <th class="sorting text-center" style="vertical-align: middle;">{{ __('tenant/history.unit') }}</th>
		                                    <th class="sorting text-center" style="vertical-align: middle;">{{ __('tenant/history.description') }}</th>
		                                    <th class="sorting text-center" style="width: 177px; vertical-align: middle;">{{ __('tenant/history.start_overtime') }}</th>
		                                    <th class="sorting text-center" style="width: 177px; vertical-align: middle;">{{ __('tenant/history.end_overtime') }}</th>
		                                    <th class="sorting text-center" style="width: 10px; vertical-align: middle;">{{ __('common.status') }}</th>
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
    	var OT_STATUS = @json(__('tenant/history.overtime_statuses'));
    	$(document).ready(function(){
	    	$('#tblOvertime').DataTable({
	    		processing: true,
	    		serverSide: true,
	    		paging: false,
	    		responsive: true,
	    		ajax : {
		            url : "{{ url('/tenant/hovertimeTable') }}",
		            data: function(data) {
		                data.date_start = $("#start").val();
		                data.date_end = $("#end").val();
		            },
		        },
		        columns: [
		            {data:null,
		            	render: function (data, type, row, meta) {
			                return meta.row + meta.settings._iDisplayStart + 1 +'.';
			            }
		            },
		            {data:"id"},
		            {data:"date_created",
		            	render: function (data, type, row, meta) {
			                if (data==null)
			                {
			                	date = ' - ';
			                }
			                return moment(data).format('DD MMMM YYYY');
			            }
		        	},
		            {data:"lot_no"},
		            {data:"description"},
		            {data:"start_overtime",
		            	render: function (data, type, row, meta) {
			                if (data==null)
			                {
			                	date = ' - ';
			                }
			                return moment(data).format('DD MMMM YYYY H:mm:ss');
			            }
		        	},
		        	{data:"end_overtime",
		            	render: function (data, type, row, meta) {
			                if (data==null)
			                {
			                	date = ' - ';
			                }
			                return moment(data).format('DD MMMM YYYY H:mm:ss');
			            }
		        	},
		            {data:"status",
		                render: function (data, type, row) {
		                    if (data=='N'){
		                        status = OT_STATUS.N;
		                        color = 'badge-soft-info';
		                    }
		                    else if (data=='A') {
		                        status = OT_STATUS.A;
		                        color = 'badge-soft-success';
		                    }
		                    else if (data=='X') {
		                        status = OT_STATUS.X;
		                        color = 'badge-soft-warning';
		                    }
		                    else {
		                    	status = OT_STATUS.closed;
		                        color = 'badge-soft-danger';
		                    }
		                    return '<span class="badge '+color+'">'+status+'</span>'
		                }
		            },
		        ],
		        dom : "Bfrtip",
		        buttons: [
		            {
		                extend: 'pdf',
		                title: @json(__('tenant/history.overtime_title')),
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
		                    url : "{{url('/tenant/hovertimeSearch')}}",
		                    type:"POST",
		                    data: datafrm,
		                    dataType:"json",
		                    success:function(event, data)
		                    {
		                        if(event.Error==false)
		                        {
		                            $('#tblOvertime').DataTable().ajax.reload();
		                        }
		                        else {
		                            Swal.fire({
		                                title: event.Pesan,
		                                // text: event.Pesan,
		                                icon:"warning",
		                                confirmButtonText: @json(__('common.ok'))
		                            });
		                            $('#tblOvertime').DataTable().ajax.reload();
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