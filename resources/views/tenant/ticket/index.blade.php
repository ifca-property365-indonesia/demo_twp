@extends('tenant.template.base')
@section('content')
	<div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title"><?php echo $jdl?></h3>
                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div><!-- .nk-block-head -->
    	<div class="card card-preview">
    		<div class="card-inner">
				<div class="card-title-group">
					<div class="card-title">
						<h6 class="title">
							<span class="mr-2">Tenant Ticket</span>
						</h6>
					</div>
				</div>
				<br/>
    			<form class="form-horizontal" id="frm" enctype="multipart/form-data" method="POST" action="">
    				@csrf
					<div class="col-md-12">
	                    <div class="form-group row">
	                    	<div class="col-6">
	                    		<label class="col-xs-2 form-label">Ticket Type <span class="text-danger">*</span></label>
			                    <div class="col-xs-10">
			                        <select name="ticket_type" id="ticket_type" class="form-control select2" data-placeholder="Choose a Ticket Type">
			                        	<option value=""></option>
			                        	<option value="R">Request</option>
			                        	<option value="C">Complain</option>
			                        </select>
			                    </div>
	                    	</div>
	                    	<div class="col-6">
	                    		<label class="col-xs-2 form-label">Tenant <span class="text-danger">*</span></label>
			                    <div class="col-xs-10">
			                    	<select name="tenant_no" id="tenant_no" class="form-control select2" data-placeholder="Choose a Tenant">
			                    		<?php echo $combo_tenant; ?>
			                    	</select>  
			                    </div>
	                    	</div>
	                    </div>
	                    <div class="form-group row">
	                    	<div class="col-6">
	                    		<label class="col-xs-2 form-label">Unit <span class="text-danger">*</span></label>
		                    	<div class="col-xs-10">
		                        	<select name="lot_no" id="lot_no" class="form-control select2" data-placeholder="Choose a Unit">
		                        		<option value=""></option>
		                        	</select>  
		                    	</div>
	                    	</div>
	                    	<div class="col-6">
	                    		<label class="col-xs-2 form-label">Floor <span class="text-danger">*</span></label>
		                    	<div class="col-xs-10">
		                        	<input type="text" class="form-control" value='' name="floor" id="floor" readonly="readonly" />  
		                    	</div>
	                    	</div>
	                    </div>
	                    <div class="form-group row">
	                    	<label class="col-2 form-label">Ticket Number </label>
		                    <div class="col-10">
		                        <input type="text" class="form-control" name="angka" id="angka" readonly="readonly" />
		                    </div>
		                    <div class="col-10">
		                        <input type="text" class="form-control" name="pre" id="pre" readonly="readonly" hidden/>
		                    </div>
	                    </div>
	                    <div class="form-group row">
	                    	<label class="col-2 form-label">Location <span class="text-danger">*</span></label>
	                    	<div class="col-10">
	                        	<input type="text" class="form-control" maxlength="20" id="location" name="location" value="" />  
	                    	</div>
	                    </div>
	                    <div class="form-group row">
	                    	<label class="col-2 form-label">Requested By <span class="text-danger">*</span></label>
	                    	<div class="col-10">
	                        	<input type="text" class="form-control" id="req_by" name="req_by" value="" />
	                    	</div>
	                    </div>
	                    <div class="form-group row">
	                    	<label class="col-2 form-label">Contact No <span class="text-danger">*</span></label>
	                    	<div class="col-10">
	                        	<input type="text" class="form-control" maxlength="20" id="contact_no" name="contact_no" value="" />
	                    	</div>
	                    </div>
	                    <div class="form-group row">
	                    	<label class="col-2 form-label">Category <span class="text-danger">*</span> </label>
	                    	<div class="col-10">
	                        	<select name="category" id="category" class="form-control select2" data-placeholder="Choose a Category" disabled>
	                        		<option value=""></option>
								</select>
	                    	</div>
	                    </div>
	                    <div class="form-group row">
	                    	<label class="col-2 form-label">Description <span class="text-danger">*</span></label>
	                    	<div class="col-10">
	                    		<textarea class="form-control" rows="3" maxlength="255" placeholder="Complain Description" id="description" name="description"></textarea>
	                    	</div>
	                    </div>
	                    <div class="form-group row">
	                    	<label class="col-2 form-label">Picture </label>
	                    	<div class="col-10">
	                    		<div class="form-control-wrap">
        							<div class="custom-file">
        								<input type="file" id="ticket_image" name="ticket_image" class="custom-file-input" accept="image/*">
        								<label class="custom-file-label" for="ticket_image" id="pictname">Choose File</label>
        								<p style="color: red">(* Max Upload Size 2MB. Only JPG, JPEG, PNG, GIF allowed)</p>
        							</div>
        						</div>
	                    	</div>
	                    </div>
	                    <div class="form-group">
        					<div class="col-xs-4">
        						<img src="" id="picturebox" class="img-responsive">
        						<input type="hidden" class="form-control" name="picturepath" id="picturepath" value="https://i0.wp.com/www.winhelponline.com/blog/wp-content/uploads/2017/12/user.png?resize=256%2C256&quality=100&ssl=1" readonly><input type="hidden" class="form-control" name="picturename" id="picturename" readonly><input type="hidden" class="form-control" name="pictureattach" id="pictureattach" readonly>
        					</div>
			            </div>
		            </div>
		            <div style="text-align:right;margin-right: 50px;margin-top: 20px">
        				<button type="button" id="btnSave" class="btn btn-primary">Submit</button>
	                </div>
		            <input type="hidden" name="entity" id="entity" />
		            <input type="hidden" name="project" id="project" />
		        </form>
		    </div>
		</div>
    </div>
	<div id="overlaySpinner" class="spinner-overlay">
		<div class="spinner-box">
			<div class="spinner"></div>
			<div class="loading-text">
				Processing, please wait...
			</div>
		</div>
	</div>
	

    <script type="text/javascript">
    	$(document).ready(function(){
    		loaddata();

			function loadHargaItem()
			{
				$('#resultHargaItem').html('Loading...');

				$.ajax({
					url: "<?= url('tenant/ticketharga/getHargaItem') ?>",
					type: "GET",

					success: function(res){

						$('#resultHargaItem').html(res);

						$('#tblHargaItem').DataTable({
							pageLength: 20,
							destroy: true,
							dom: 'Bfrtip',

							buttons: [
								{
									extend: 'pdf',
									title: 'Harga Item',
									className: 'btn btn-primary mb-2',
									text: '<em class="icon ni ni-download"></em>&nbsp;Generate PDF',

									init: function(api, node, config) {
										$(node).removeClass('dt-button');
									},
								},
							]
						});

					},

					error: function(xhr){
						console.log(xhr.responseText);
					}
				});
			}

			$('#modalHargaItem').on('show.bs.modal', function () {

				loadHargaItem();

			});

			function loadHargaJasa()
			{
				$('#resultHargaJasa').html('Loading...');

				$.ajax({
					url: "<?= url('tenant/ticketharga/getHargaJasa') ?>",
					type: "GET",

					success: function(res){

						$('#resultHargaJasa').html(res);

						$('#tblHargaJasa').DataTable({
							pageLength: 20,
							destroy: true,
							dom: 'Bfrtip',

							buttons: [
								{
									extend: 'pdf',
									title: 'Harga Item',
									className: 'btn btn-primary mb-2',
									text: '<em class="icon ni ni-download"></em>&nbsp;Generate PDF',

									init: function(api, node, config) {
										$(node).removeClass('dt-button');
									},
								},
							]
						});

					},

					error: function(xhr){
						console.log(xhr.responseText);
					}
				});
			}

			$('#modalHargaJasa').on('show.bs.modal', function () {

				loadHargaJasa();

			});

    		$('.select2').select2();

    		$('#ticket_type').change(function() {
    			var ticket_type = $(this).find(':selected').val();
    			var site_url = "{{ url('tenant/ticket/getCat') }}";
				$.post(site_url,
					{
						"_token": "{{ csrf_token() }}",
						ticket_type:ticket_type
					},
    				function(data, status) {
		                $("#category").empty();
		                $("#category").attr('disabled', false);
		                $("#category").append(data);
		            }
	            );
	        });

	        $('#tenant_no').change(function() {
	        	var tenant_no = $(this).find(':selected').val();
	        	var ent = $(this).find(':selected').data("entity");
	        	var prj = $(this).find(':selected').data("project");
	        	if(tenant_no!=='') {
	        		var site_url = "{{ url('tenant/ticket/getLotNo') }}";
	        		$.post(site_url,
						{
							"_token": "{{ csrf_token() }}",
							id_tenancy: tenant_no
						},
						function(data, status) {
							$("#lot_no").empty();
							$("#lot_no").append(data);
						}
					);
	        	} else {
		            $("#lot_no").empty();
		            $("#floor").empty();
		            $('#floor').val(null);
		        }
		    });

		    $('#lot_no').change(function() {
				var lvl_no = $("#lot_no option:selected").data("level");
				var ent = $("#tenant_no option:selected").data("entity");
				var prj = $("#tenant_no option:selected").data("project");

				$("#floor").empty();
				$("#floor").val(lvl_no);
				$("#entity").val(ent);
				$("#project").val(prj);
			});

			$("#ticket_image").on('change', function ()
		    {
			    $.ajax({
	            	url : "{{url('tenant/ticket/savepic')}}",
	            	type:"POST",
	            	data: function () {
	            		var data = new FormData();
	            		data.append("_token", "{{ csrf_token() }}");
			            data.append("ticket_image", $("#ticket_image").get(0).files[0]);
			            data.append("req_by", $('#req_by').val());
			            data.append("ticket_type", $('#ticket_type').val());
			            return data;
			        }(),
			        processData: false,
			        contentType: false,
			        dataType:"json",
			        success:function(data, status){
			            console.log(data.status);
			            if(data.status == "OK"){
							Swal.fire({
								title: "Information",
								text: data.pesan,
								icon: "success",
								confirmButtonText: "OK"
							});
							$('#picturebox').attr('src', data.url);
							$('#picturepath').val(data.url)
							$('#picturename').val(data.picname)
							$('#pictureattach').val(data.pic_attached)
			            } else {
							Swal.fire({
								title: "Error",
								text: data.pesan,
								icon: "error",
								confirmButtonText: "OK"
							});
			            }
			        },
			            error: function(jqXHR, textStatus, errorThrown){
			            Swal.fire(textStatus+' Save : '+errorThrown);
			        }
			    });
	        });

			$("#frm").validate({
			    ignore: [],
			    rules: {
			        ticket_type: { required: true },
			        tenant_no: { required: true },
			        lot_no: { required: true },
			        location: { required: true },
			        req_by: { required: true },
			        contact_no: { required: true },
			        category: { required: true },
			        description: { required: true }
			    },
			    messages: {
			        ticket_type: "Please select a type",
			        tenant_no: "Please select a tenant",
					lot_no: "Please select a lot",
			        location: "Please select a location",
					req_by: "Please select a req_by",
			        contact_no: "Please select a contact_no",
					category: "Please select a category",
			        description: "Please select a description",
			    },
			    errorElement: "span",
			    highlight: function (element, errorClass, validClass) {
			        $(element).addClass(errorClass);
			        $(element).closest('.form-group')
			            .removeClass('has-success')
			            .addClass('has-error');
			    },
			    unhighlight: function (element, errorClass, validClass) {
			        $(element).removeClass(errorClass);
			        $(element).closest('.form-group')
			            .removeClass('has-error')
			            .addClass('has-success');
			    },
			    errorPlacement: function (error, element) {
			        if (element.parent('.input-group').length) {
			            error.insertAfter(element.parent());
			        } else if (element.hasClass('select2')) {
			            error.insertAfter(element.next('span'));
			        } else {
			            error.insertAfter(element);
			        }
			    }
			});

		    // simpan&edit data
		    $('#btnSave').click(function(event) {

    			event.preventDefault();

				if ($('#frm').valid()) {

					var id = '<?php echo $id?>';
					var action = '<?php echo $form?>';
					var tenant_no = $('#tenant_no').val();

					var datafrm = $('#frm').serializeArray();

					datafrm.push(
						{name:"action", value:action},
						{name:"id", value:id},
						{name:"tenant_no", value:tenant_no}
					);

					// Disable button
					$('#btnSave').prop('disabled', true);

					// Tampilkan loading
					$('#overlaySpinner').css('display', 'flex');

					// Simpan waktu mulai
					var startTime = Date.now();

					$.ajax({
						url: "{{ url('api/ticket/save') }}",
						type: "POST",
						data: datafrm,
						dataType: "json",

						success: function(event) {

							// Hitung berapa lama AJAX sudah berjalan
							var elapsed = Date.now() - startTime;

							// Minimal tampil 3 detik
							var remaining = Math.max(0, 3000 - elapsed);

							setTimeout(function() {

								$('#overlaySpinner').hide();

								if (event.status == 'OK') {

									Swal.fire({
										title: "Information",
										icon: "success",
										text: event.pesan,
										confirmButtonText: "OK"
									}).then(function () {
										window.location.href = "{{ url('/tenant/dash') }}";
									});

								} else {

									$('#btnSave').prop('disabled', false);

									Swal.fire({
										title: "Information",
										icon: "error",
										text: event.pesan,
										confirmButtonText: "OK"
									});
								}

							}, remaining);
						},

						error: function(jqXHR, textStatus, errorThrown) {

							var elapsed = Date.now() - startTime;
							var remaining = Math.max(0, 3000 - elapsed);

							setTimeout(function() {

								$('#overlaySpinner').hide();
								$('#btnSave').prop('disabled', false);

								Swal.fire({
									title: "Error",
									icon: "error",
									text: textStatus + ' Save : ' + errorThrown,
									confirmButtonText: "OK"
								});

							}, remaining);
						}
					});
				}
			});

		    function loaddata(){
				var id = '<?php echo $id ?>';
				console.log("ID:", id);

				if (id > 0) 
				{
					$.getJSON("{{url('/tenant/ticket')}}" + "/" + id, function (data) {

						$('#angka').val(data[0].complain_no);
						$('#pre').val(data[0].complain_no.replace(/[0-9]/g, ''));
						$('#ticket_type').val(data[0].complain_type).trigger('change');
						$('#tenant_no').val(data[0].id_tenancy).trigger('change');
						$('#tenant_no').attr('disabled', true);

						$("#lot_no").data("selected", data[0].lot_no); // simpan sementara

						getlotno(data[0].id_tenancy, data[0].lot_no);

						$('#floor').val(data[0].floor);
						$('#location').val(data[0].location);
						$('#req_by').val(data[0].serv_req_by);
						$('#contact_no').val(data[0].contact_no);

						getcategory(data[0].complain_type, data[0].category_cd);

						$('#description').val(data[0].work_requested);

						if (data[0].picture != "") {
							$('#picturebox').attr("src", data[0].picture);
							$('#picturepath').val(data[0].picture);
						}

						// === 🔥 BARU SET VALUE DI SINI ===
						setTimeout(() => {
							const val = $("#lot_no").data("selected");
							$('#lot_no').val(val).trigger('change');
							console.log("Final selected:", val);
						}, 500);
					});
				}
				else 
				{
					// 🔹 Buat data baru
					$('#tenant_no').change(function() {
						var tenant_no = $(this).find(':selected').val();
						var ent = $(this).find(':selected').data("entity");
						var prj = $(this).find(':selected').data("project");
						console.log(tenant_no);

						if (tenant_no !== '') {
							var site_url = "{{ url('tenant/ticket/getLotNo') }}";

							$.post(site_url, {
								"_token": "{{ csrf_token() }}",
								tenant_no: tenant_no
							}, function(data, status) {

								$("#lot_no").empty().append(data);

								var site_url2 = "{{ url('tenant/ticket/getTicketNew') }}/" + ent + "/" + prj;

								$.get(site_url2, {
									"_token": "{{ csrf_token() }}",
									ent: ent,
									prj: prj
								}).then(function(datas) {
									console.log('datas:', datas);
									$('#angka').val(datas); // sekalian isi kalau perlu
								});

							});

						} else {
							$("#lot_no").empty();
							$("#floor").val(null);
						}
					});
					

					// 🔹 AUTO SELECT jika hanya ada satu tenant
					setTimeout(function() {
						var $tenantSelect = $('#tenant_no');
						var options = $tenantSelect.find('option');

						// cek jika hanya 1 opsi valid (bukan placeholder kosong)
						if (options.length === 1 || 
							(options.length === 2 && options.first().val() === '')) {

							// pilih opsi yang valid
							var onlyOption = (options.first().val() === '') ? options.eq(1).val() : options.first().val();
							$tenantSelect.val(onlyOption).trigger('change');
							console.log("Auto-selected tenant:", onlyOption);
						}
					}, 500); // kasih delay sedikit biar dropdown sempat di-render
				}
			}

			function getlotno(tenant_no, lot_no, callback) {
				var site_url = "{{ url('tenant/ticket/getLotNoEdit') }}" + "/" + tenant_no + "/" + lot_no;

				$.getJSON(site_url, function(data) {
					console.log("Raw response:", data);
					$("#lot_no").empty().append(data).trigger('change');
					if (callback) callback();
				});
			}

		    function getcategory(complain_type, category_cd) {
		        var site_url = "{{ url('tenant/ticket/getCatEdit') }}" + "/" + complain_type + "/" + category_cd;
        		$.getJSON(site_url, function(data) {
					$("#category").empty();
		            $("#category").append(data);
		            $("#category").trigger("change");
				});
		    }
    	})
    </script>
@endsection