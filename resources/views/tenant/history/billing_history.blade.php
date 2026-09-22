@extends('tenant.template.base')
@section('content')
	<div class="page-body">
        <div class="page-head">
            <div class="page-head-row">
                <div class="page-head-content">
                    <h3 class="page-title">Billing History</h3>
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
                            <label for="start" class="form-label">Start Date</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                <input type="text" id="start" name="start" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="{{ date('d/m/Y') }}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <label for="end" class="form-label">End Date</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                <input type="text" id="end" name="end" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="{{ date('d/m/Y') }}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-4 col-lg-2">
                            <button type="submit" id="search" class="btn btn-primary w-100"><i class="cil-search"></i><span>Search</span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

	                <div class="card">
	                	<div class="card-body">
	                		<div class="table-responsive">
	                			<table id="tblBilling" class="table table-bordered table-striped" role="grid" aria-describedby="tblBilling_info">
		                            <thead class="table-dark">
		                                <tr role="row">
		                                    <th class="sorting text-center" style="width: 7px; vertical-align: middle;">No.</th>
		                                    <th class="sorting text-center" style="width: 24px;">Document Number</th>
		                                    <th class="sorting text-center" style="width: 100px;">Doc Date</th>
		                                    <th class="sorting text-center" style="width: 100px;">Due Date</th>
		                                    <th class="sorting text-center" style="vertical-align: middle;">Description</th>
		                                    <th class="sorting text-center" style="width: 110px; vertical-align: middle;">Periode</th>
		                                    <th class="sorting text-center" style="width: 1px; vertical-align: middle;">Currency</th>
		                                    <th class="sorting" style="vertical-align: middle;">Amount</th>
		                                    <th class="sorting" style="vertical-align: middle;">Paid</th>
											<th class="sorting" style="vertical-align: middle;">Paid Date</th>
		                                    <th class="sorting text-center" style="vertical-align: middle;">Outstanding</th>
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
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
    	$(document).ready(function(){
	    	$('#tblBilling').DataTable({
	    		processing: true,
	    		serverSide: true,
	    		paging: false,
	    		// responsive: true,
	    		ajax : {
		            url : "{{ url('/tenant/gethistorybillingtable') }}",
					type: 'POST', // 🔥 INI FIX UTAMA
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
		            {data:"doc_no"},
		            {data:"doc_date",
		            	render: function (data, type, row, meta) {
			                if (data==null)
			                {
			                	date = ' - ';
			                }
			                return moment(data).format('DD MMMM YYYY');
			            }
		        	},
		        	{data:"due_date",
		            	render: function (data, type, row, meta) {
			                if (data==null)
			                {
			                	date = ' - ';
			                }
			                return moment(data).format('DD MMMM YYYY');
			            }
		        	},
		            {data:"ar_ldg_desc"},
		            {data:null,
		            	render: function (data, type, row, meta) {
			                if (row.start_date==null || row.end_date==null)
			                {
			                	dt = ' - ';
			                }
			                var periode = moment(row.start_date).format('DD MMMM YYYY') + ' - ' + moment(row.end_date).format('DD MMMM YYYY');
			                if (periode == 'Invalid date - Invalid date')
			                {
			                	result = ' - ';
			                }
			                else {
			                	result = periode;
			                }
			                return result;
			            }
		        	},
		        	{data:"currency_cd"},
		        	{data:"fdoc_amt", className: "text-end text-nowrap",
			        	render: function (data, type, row) {
	                    	return number_format(data);
	                    }
		        	},
		        	{data:"alloc_amt", className: "text-end text-nowrap",
		        		render: function (data, type, row) {
	                    	return number_format(data);
	                    }
		        	},
					{
						data: "credit_date",
						render: function (data, type, row, meta) {
							if (!data || !moment(data).isValid()) {
								return ' - ';
							}

							return moment(data).format('DD MMMM YYYY');
						}
					},
		        	{data:null, className: "text-end text-nowrap",
		        		render: function (data, type, row) {
		        			var sisa = row.fdoc_amt - row.alloc_amt;
	                    	return number_format(sisa);
	                    }
		        	},
		        ],
		        dom : "Bfrtip",
		        buttons: [
    {
        extend: 'pdf',
        title: 'Billing History',
        orientation: 'landscape', // <- ini yang bikin landscape
        pageSize: 'A4',           // optional, biar jelas
        className: 'btn btn-primary mb-2',
        text: '<i class="cil-cloud-download"></i>&nbsp;Generate PDF',
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
		                    url : "{{url('/tenant/hbillingSearch')}}",
		                    type:"POST",
		                    data: datafrm,
		                    dataType:"json",
		                    success:function(event, data)
		                    {
		                        if(event.Error==false)
		                        {
		                            $('#tblBilling').DataTable().ajax.reload();
		                        }
		                        else {
		                            Swal.fire({
		                                title: event.Pesan,
		                                // text: event.Pesan,
		                                icon:"warning",
		                                confirmButtonText: "OK"
		                            });
		                            $('#tblBilling').DataTable().ajax.reload();
		                        }
		                    },
		                    error: function(jqXHR, textStatus, errorThrown){
		                        Swal.fire({
		                            title: "Error",
		                            animation: false,
		                            icon:"error",
		                            text: textStatus+' Search : '+errorThrown,
		                            confirmButtonText: "OK"
		                        });
		                    }
		                });
		            }
		        }
		    });
	    });

		function number_format (number, decimals, dec_point, thousands_sep)
		{
	        // Strip all characters but numerical ones.
	        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	        var n = !isFinite(+number) ? 0 : +number,
	            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	            s = '',
	            toFixedFix = function (n, prec) {
	                var k = Math.pow(10, prec);
	                return '' + Math.round(n * k) / k;
	            };
	        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
	        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	        if (s[0].length > 3) {
	            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	        }
	        if ((s[1] || '').length < prec) {
	            s[1] = s[1] || '';
	            s[1] += new Array(prec - s[1].length + 1).join('0');
	        }
	        return s.join(dec);
	    }
    </script>
@endsection