@extends('tenant.template.base')
@section('content')
	<div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Ticket History</h3>
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
	                                <input type="text" id="start" name="start" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="<?php echo date('01/m/Y'); ?>" required>
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
	                			<table id="tblTicket" class="table table-bordered table-striped" role="grid" aria-describedby="tblTicket_info">
		                            <thead style="background:#101924; color: #ffffff;">
		                                <tr role="row">
		                                    <th class="sorting text-center" style="width: 7px; vertical-align: middle;">No.</th>
		                                    <th class="sorting text-center" style="width: 24px;">Ticket Number</th>
		                                    <th class="sorting text-center" style="width: 24px; vertical-align: middle;">Category</th>
		                                    <th class="sorting text-center" style="vertical-align: middle;">Description</th>
		                                    <th class="sorting text-center" style="width: 100px; vertical-align: middle;">Reported Date</th>
		                                    <th class="sorting text-center" style="width: 24px;">Request By</th>
		                                    <th class="sorting text-center" style="width: 80px; vertical-align: middle;">Lot No</th>
		                                    <th class="sorting text-center" style="width: 10px;">Ticket Status</th>
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
    	$(document).ready(function(){
			$('.date-picker').datepicker('setEndDate', new Date());
	    	$('#tblTicket').DataTable({
	    		processing: true,
	    		serverSide: true,
	    		paging: false,
	    		responsive: true,
	    		ajax : {
		            url : "{{ url('/tenant/hticketTable') }}",
		            headers: {
		                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		            },
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
		            {data:"complain_no"},
		            {data:"category_desc"},
		            {data:"work_requested"},
		            {data:"reported_date",
		            	render: function (data, type, row, meta) {
			                if (data==null)
			                {
			                	date = ' - ';
			                }
			                return moment(data).format('DD MMMM YYYY');
			            }
		        	},
		            {data:"serv_req_by"},
		            {data:"lot_no"},
		            {data:"status",
		                render: function (data, type, row) {
		                    if (data=='A') {
		                        status = "Accepted";
		                        color = 'badge-outline-primary';
		                    } else if (data=='S') {
		                        status = "Survey";
		                        color = 'badge-outline-primary';
		                    } else if (data=='P') {
		                        status = "Process";
		                        color = 'badge-outline-primary';
		                    } else if (data=='F') {
		                        status = "Confirm";
		                        color = 'badge-outline-primary';
		                    } else if (data=='M') {
		                        status = "Modify";
		                        color = 'badge-outline-primary';
		                    } else if (data=='Z') {
		                        status = "Charged Approved";
		                        color = 'badge-outline-warning';
		                    } else if (data=='Y') {
		                        status = "Approved";
		                        color = 'badge-outline-success';
		                    } else if (data=='C') {
		                        status = "Closed";
		                        color = 'badge-outline-success';
		                    } else if (data=='R'){
		                        status = 'Open';
		                        color = 'badge-outline-info';
		                    } else if (data=='X'){
		                    	status = "Cancel";
		                        color = 'badge-outline-default';
		                    } 
		                    return '<span class="badge badge-sm badge-dim '+color+' d-none d-md-inline-flex">'+status+'</span>'
		                }
		            },
		        ],
		        dom : "Bfrtip",
		        buttons: [
		            {
		                extend: 'pdf',
		                title: 'Ticket History',
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
		                                confirmButtonText: "OK"
		                            });
		                            $('#tblTicket').DataTable().ajax.reload();
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
    </script>
@endsection