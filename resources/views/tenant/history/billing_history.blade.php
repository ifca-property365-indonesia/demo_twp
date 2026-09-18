@extends('tenant.template.base')
@section('content')
	<div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Billing History</h3>
                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div><!-- .nk-block-head -->
        <div class="nk-block">
        	<div class="row g-gs">
	            <div class="col-sm-12">
	                <form class="form form-horizontal form-validate" id="form_search" method="POST" action="" novalidate="novalidate">
	                	@csrf
	                    <div class="row">
	                        <div class="col-sm-2">
	                            <label for="start" class=""> Start Date </label>
	                        </div>
	                        <div class="col-sm-3">
	                            <div class="form-control-wrap">
	                                <div class="form-icon form-icon-left">
	                                    <em class="icon ni ni-calendar"></em>
	                                </div>
	                                <input type="text" id="start" name="start" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="<?php $mydate=date("d/m/Y");echo "$mydate";?>" required>
	                            </div>
	                        </div>
	                    </div>

	                    <div class="row mt-2">
	                        <div class="col-sm-2">
	                            <label for="end" class="control-label"> End Date </label>
	                        </div>
	                        <div class="col-sm-3">
	                            <div class="form-control-wrap">
	                                <div class="form-icon form-icon-left">
	                                    <em class="icon ni ni-calendar"></em>
	                                </div>
	                                <input type="text" id="end" name="end" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="<?php $mydate=date("d/m/Y");echo "$mydate";?>" required>
	                            </div>
	                        </div>
	                        <div class="col-sm-3">
	                            <button type="submit" id="search" class="btn btn-info">
	                                <em class="icon ni ni-search"></em>
	                                <span>Search</span>
	                            </button>
	                        </div>
	                    </div>
	                </form>

	                <div class="card card-bordered mt-3">
	                	<div class="card-inner">
	                		<div class="table-responsive mt-3">
	                			<table id="tblBilling" class="table table-bordered table-striped" role="grid" aria-describedby="tblBilling_info">
		                            <thead style="background:#101924; color: #ffffff;">
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
		        	{data:"fdoc_amt", className: "text-right text-nowrap",
			        	render: function (data, type, row) {
	                    	return number_format(data);
	                    }
		        	},
		        	{data:"alloc_amt", className: "text-right text-nowrap",
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
		        	{data:null, className: "text-right text-nowrap",
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
        text: '<em class="icon ni ni-download"></em>&nbsp;Generate PDF',
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