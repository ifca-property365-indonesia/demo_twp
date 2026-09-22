@extends('tenant.template.base')
@section('content')
	<div class="nk-content-body">
		<div class="nk-block-head nk-block-head-sm">
			<div class="nk-block-between">
				<div class="nk-block-head-content">
					<h3 class="nk-block-title page-title">Permit History</h3>
				</div><!-- .nk-block-head-content -->
			</div><!-- .nk-block-between -->
		</div><!-- .nk-block-head -->
		<div class="nk-block">
			<div class="row g-gs">
				<div class="col-sm-12">
					<div class="card-title-group">
						<div class="card-title">
							<h6 class="title">
								<span class="mr-2">Permit History</span>
							</h6>
						</div>
					</div>
					<br/>
					<form class="form form-horizontal" id="form_search" method="POST" action="" novalidate="novalidate">
						@csrf
						<div class="row">
							<div class="col-sm-2">
								<label for="permit_no" class="control-label"> Permit No </label>
							</div>
							<div class="col-sm-3">
								<div class="form-control-wrap">
									<input type="text" id="permit_no" name="permit_no" class="form-control" placeholder="All permit no" autocomplete="off">
								</div>
							</div>
						</div>

						<div class="row mt-2">
							<div class="col-sm-2">
								<label for="permit_type" class="control-label"> Permit Type </label>
							</div>
							<div class="col-sm-3">
								<div class="form-control-wrap">
									<select id="permit_type" name="permit_type" class="form-control">
										<option value="">All type</option>
										<option value="W">Work Permit</option>
										<option value="I">Entry Permit of Goods</option>
										<option value="O">Exit Permit of Goods</option>
									</select>
								</div>
							</div>
						</div>

						<div class="row mt-2">
							<div class="col-sm-2">
								<label for="start_date" class="control-label"> Start Date </label>
							</div>
							<div class="col-sm-3">
								<div class="form-control-wrap">
									<div class="form-icon form-icon-left">
										<em class="icon ni ni-calendar"></em>
									</div>
									<input type="text" id="start_date" name="start_date" class="form-control date-picker" data-date-format="dd/mm/yyyy" placeholder="All date" value="" autocomplete="off">
								</div>
							</div>
						</div>

						<div class="row mt-2">
							<div class="col-sm-2"></div>
							<div class="col-sm-4">
								<button type="submit" id="search" class="btn btn-info">
									<em class="icon ni ni-search"></em>
									<span>Search</span>
								</button>
								<button type="button" id="reset" class="btn btn-light ml-1">
									<em class="icon ni ni-reload"></em>
									<span>Reset</span>
								</button>
							</div>
						</div>
					</form>

					<div class="card card-bordered mt-3">
						<div class="card-inner">
							<div class="table-responsive mt-3">
								<table id="tblPermit" class="table table-bordered table-striped" role="grid" aria-describedby="tblPermit_info">
									<thead style="background:#101924; color: #ffffff;">
										<tr role="row">
											<th class="text-center" style="width: 7px; vertical-align: middle;">No.</th>
											<th class="text-center" style="width: 24px; vertical-align: middle;">Permit No</th>
											<th class="text-center" style="width: 24px; vertical-align: middle;">Permit Type</th>
											<th class="text-center" style="width: 60px; vertical-align: middle;">Tower</th>
											<th class="text-center" style="width: 40px; vertical-align: middle;">Floor</th>
											<th class="text-center" style="width: 60px; vertical-align: middle;">Unit</th>
											<th class="text-center" style="vertical-align: middle;">Description</th>
											<th class="text-center" style="width: 100px; vertical-align: middle;">Start Date</th>
											<th class="text-center" style="width: 100px; vertical-align: middle;">End Date</th>
											<th class="text-center" style="width: 80px; vertical-align: middle;">Time</th>
											<th class="text-center" style="width: 40px; vertical-align: middle;">Print</th>
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
			var dash = function (data) {
				return (data == null || data === '') ? '-' : data;
			};

			var fmtDate = function (data) {
				return (data == null || data === '') ? '-' : moment(data).format('DD MMMM YYYY');
			};

			var tblPermit = $('#tblPermit').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				pageLength: 10,
				// Urutan default (tanggal input permit, terbaru di atas) diatur di sisi server.
				order: [],
				ajax : {
					url : "{{ url('/tenant/permit/historyTable') }}",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					data: function(data) {
						data.permit_no = $("#permit_no").val();
						data.permit_type = $("#permit_type").val();
						data.start_date = $("#start_date").val();
					},
				},
				columns: [
					{data:"DT_RowIndex", orderable: false, searchable: false,
						render: function (data) {
							return data + '.';
						}
					},
					{data:"complain_no"},
					{data:"complain_type",
						render: function (data) {
							if (data == 'W') {
								return 'Work Permit';
							} else if (data == 'I') {
								return 'Entry Permit of Goods';
							} else if (data == 'O') {
								return 'Exit Permit of Goods';
							}
							return dash(data);
						}
					},
					{data:"tower", render: function (data) { return dash(data); }},
					{data:"floor", render: function (data) { return dash(data); }},
					{data:"unit", render: function (data) { return dash(data); }},
					{data:"note", render: function (data) { return dash(data); }},
					{data:"start_date", render: function (data) { return fmtDate(data); }},
					{data:"end_date", render: function (data) { return fmtDate(data); }},
					{data:"start_time",
						render: function (data, type, row) {
							if (data == null || data === '') {
								return '-';
							}
							var end = (row.end_time == null || row.end_time === '') ? '' : ' - ' + row.end_time;
							return data + end;
						}
					},
					{data:"complain_no", orderable: false, searchable: false, className: 'text-center',
						render: function (data) {
							var url = "{{ url('/tenant/permit/print') }}/" + encodeURIComponent(data);
							return '<a href="' + url + '" target="_blank" class="btn btn-sm btn-outline-primary" title="Print PDF">'
								+ '<em class="icon ni ni-printer"></em></a>';
						}
					},
				],
				dom : "Bfrtip",
				buttons: [
					{
						className: 'btn btn-primary mb-2',
						text: '<em class="icon ni ni-plus"></em>&nbsp;Request Permit',
						action: function (e, dt, node, config) {
							window.location.href = "{{ url('/tenant/permit/add') }}";
						},
						init: function(api, node, config) {
							$(node).removeClass('dt-button')
						},
					},
				]
			});

			$('#form_search').submit(function(event){
				event.preventDefault();
				tblPermit.ajax.reload();
			});

			$('#reset').click(function(event){
				event.preventDefault();
				$('#permit_no').val('');
				$('#permit_type').val('');
				$('#start_date').val('');
				if ($.fn.datepicker) {
					$('#start_date').datepicker('update', '');
				}
				tblPermit.ajax.reload();
			});
		});
	</script>
@endsection
