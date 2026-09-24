@extends('tenant.template.base')

@section('title', 'Ticket')

@section('content')
    <div class="page-body">
        <div class="page-head">
            <div class="page-head-row">
                <div class="page-head-content">
                    <h3 class="page-title">{{ $jdl }}</h3>
                    <div class="page-desc">Submit a request or complaint to building management.</div>
                </div>
                <div class="page-head-content">
                    <a href="{{ url('/tenant/history/ticket') }}" class="btn btn-outline-secondary"><i class="cil-history"></i><span>Ticket History</span></a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="frm" method="POST" action="" novalidate autocomplete="off">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="ticket_type">Ticket Type <span class="text-danger">*</span></label>
                            <select name="ticket_type" id="ticket_type" class="form-control select2" data-placeholder="Choose a Ticket Type">
                                <option value=""></option>
                                <option value="R">Request</option>
                                <option value="C">Complain</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="tenant_no">Tenant <span class="text-danger">*</span></label>
                            <select name="tenant_no" id="tenant_no" class="form-control select2" data-placeholder="Choose a Tenant">
                                {!! $combo_tenant !!}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="lot_no">Unit <span class="text-danger">*</span></label>
                            <select name="lot_no" id="lot_no" class="form-control select2" data-placeholder="Choose a Unit">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="floor">Floor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="floor" id="floor" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="angka">Ticket Number</label>
                            <input type="text" class="form-control" name="angka" id="angka" readonly>
                            <input type="hidden" name="pre" id="pre">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="location">Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" maxlength="20" id="location" name="location">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="req_by">Requested By <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="req_by" name="req_by">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="contact_no">Contact No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" maxlength="20" id="contact_no" name="contact_no">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="category">Category <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-control select2" data-placeholder="Choose a Category" disabled>
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="description">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" rows="3" maxlength="255" placeholder="Describe the request / complaint" id="description" name="description"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ticket_image">Picture</label>
                            <input type="file" id="ticket_image" name="ticket_image" class="form-control" accept="image/*">
                            <div class="form-note">Max 2 MB. JPG, JPEG, PNG or GIF.</div>
                        </div>
                        <div class="col-md-6">
                            <div id="pictureWrap" class="d-none">
                                <img src="" id="picturebox" class="img-fluid rounded border" style="max-height: 180px;" alt="">
                                <div class="mt-2 d-flex align-items-center gap-2">
                                    <span class="form-note mt-0" id="pictureInfo"></span>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnRemovePicture"><i class="cil-trash"></i><span>Remove</span></button>
                                </div>
                                <div class="form-note text-primary" id="pictureHint">Picture is uploaded only when you click Submit.</div>
                            </div>
                            <input type="hidden" name="picturepath" id="picturepath" value="">
                            <input type="hidden" name="picturename" id="picturename">
                            <input type="hidden" name="pictureattach" id="pictureattach">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" id="btnReset" class="btn btn-outline-secondary"><i class="cil-reload"></i><span>Reset</span></button>
                        <button type="button" id="btnSave" class="btn btn-primary"><i class="cil-send"></i><span>Submit</span></button>
                    </div>
                    <input type="hidden" name="entity" id="entity">
                    <input type="hidden" name="project" id="project">
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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
									title: 'Item Price List',
									className: 'btn btn-primary mb-2',
									text: '<i class="cil-cloud-download"></i>&nbsp;Generate PDF',

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

			$('#modalHargaItem').on('show.coreui.modal', function () {

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
									title: 'Service Price List',
									className: 'btn btn-primary mb-2',
									text: '<i class="cil-cloud-download"></i>&nbsp;Generate PDF',

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

			$('#modalHargaJasa').on('show.coreui.modal', function () {

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

			// ------------------------------------------------------------------
			// Foto: dipilih -> hanya preview di browser. Unggah ke server baru
			// dilakukan saat Submit, jadi Reset / batal tidak meninggalkan file.
			// ------------------------------------------------------------------
			var pendingFile = null;

			function clearPicture() {
				pendingFile = null;
				$('#ticket_image').val('');
				$('#picturebox').attr('src', '');
				$('#pictureWrap').addClass('d-none');
				$('#pictureHint').removeClass('d-none');
				$('#picturepath, #picturename, #pictureattach').val('');
			}

			$('#ticket_image').on('change', function () {
				var file = this.files[0];
				if (!file) { clearPicture(); return; }
				if (!/^image\/(png|jpe?g|gif)$/i.test(file.type)) {
					Swal.fire({ title: 'Information', text: 'Only JPG, JPEG, PNG or GIF files are allowed.', icon: 'warning' });
					clearPicture();
					return;
				}
				if (file.size > 2000000) {
					Swal.fire({ title: 'Information', text: 'Maximum file size is 2 MB.', icon: 'warning' });
					clearPicture();
					return;
				}
				pendingFile = file;
				var reader = new FileReader();
				reader.onload = function (e) {
					$('#picturebox').attr('src', e.target.result);
					$('#pictureInfo').text(file.name + ' (' + Math.round(file.size / 1024) + ' KB)');
					$('#pictureWrap').removeClass('d-none');
				};
				reader.readAsDataURL(file);
			});

			$('#btnRemovePicture').on('click', clearPicture);

			// Unggah foto (dipanggil saat Submit). Mengembalikan promise.
			function uploadPicture() {
				var d = $.Deferred();
				if (!pendingFile) { return d.resolve().promise(); }

				var data = new FormData();
				data.append('ticket_image', pendingFile);
				data.append('req_by', $('#req_by').val());
				data.append('ticket_type', $('#ticket_type').val());

				$.ajax({
					url: "{{ url('tenant/ticket/savepic') }}",
					type: 'POST',
					data: data,
					processData: false,
					contentType: false,
					dataType: 'json'
				}).done(function (res) {
					if (res.status == 'OK') {
						$('#picturepath').val(res.url);
						$('#picturename').val(res.picname);
						$('#pictureattach').val(res.pic_attached);
						d.resolve();
					} else {
						d.reject(res.pesan || 'Picture upload failed.');
					}
				}).fail(function (xhr, textStatus, errorThrown) {
					d.reject('Picture upload failed: ' + textStatus + ' ' + errorThrown);
				});
				return d.promise();
			}

			// ------------------------------------------------------------------
			// Reset semua isian (foto yang belum diunggah ikut dibuang)
			// ------------------------------------------------------------------
			$('#btnReset').on('click', function () {
				Swal.fire({
					title: 'Reset the form?',
					text: 'All entered data and the selected picture will be cleared.',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes, reset',
					cancelButtonText: 'Cancel',
					reverseButtons: true
				}).then(function (r) {
					if (!r.value) { return; }
					$('#frm')[0].reset();
					$('#frm').validate().resetForm();
					$('#frm .is-invalid').removeClass('is-invalid');
					$('#ticket_type, #tenant_no').val('').trigger('change');
					$('#lot_no').empty().append('<option value=""></option>').trigger('change');
					$('#category').empty().append('<option value=""></option>').prop('disabled', true).trigger('change');
					$('#floor, #angka, #pre, #entity, #project').val('');
					clearPicture();
					$('html, body').animate({ scrollTop: 0 }, 200);
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
			    errorElement: "div",
			    errorClass: "invalid-feedback",
			    highlight: function (element) { $(element).addClass('is-invalid'); },
			    unhighlight: function (element) { $(element).removeClass('is-invalid'); },
			    errorPlacement: function (error, element) {
			        if (element.hasClass('select2')) {
			            error.insertAfter(element.next('.select2-container'));
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

					// Foto diunggah dulu (kalau ada), baru ticket disimpan
					uploadPicture().fail(function (msg) {
						$('#overlaySpinner').hide();
						$('#btnSave').prop('disabled', false);
						Swal.fire({ title: 'Error', icon: 'error', text: msg });
					}).done(function () {
					// isi field foto hasil unggah ke data form
					datafrm = datafrm.filter(function (f) { return ['picturepath', 'picturename', 'pictureattach'].indexOf(f.name) < 0; });
					datafrm.push(
						{name:"picturepath", value:$('#picturepath').val()},
						{name:"picturename", value:$('#picturename').val()},
						{name:"pictureattach", value:$('#pictureattach').val()}
					);

					$.ajax({
						url: "{{ url('api/ticket/save') }}",
						type: "POST",
						data: datafrm,
						dataType: "json",

						success: function(event) {

							// Hitung berapa lama AJAX sudah berjalan
							var elapsed = Date.now() - startTime;

							// Minimal tampil 3 detik
							var remaining = Math.max(0, 800 - elapsed);

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
							var remaining = Math.max(0, 800 - elapsed);

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
							$('#picturebox').attr("src", data[0].picture); $('#pictureInfo').text('Current picture'); $('#pictureHint').addClass('d-none'); $('#pictureWrap').removeClass('d-none');
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
								id_tenancy: tenant_no
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
@endpush