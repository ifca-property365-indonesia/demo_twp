@extends('admin.template.layout2.base')
@section('content')
<style>
.ck-editor__editable {
    min-height: 300px; /* bebas, misal 400 / 500 / 600 */
}
</style>
<link rel="stylesheet" type="text/css" href="{{url('assets/admin/css/forms/icheck/custom.css')}}">
<link rel="stylesheet" type="text/css" href="{{url('assets/admin/css/forms/icheck/icheck.css')}}">
<script src="{{url('assets/admin/js/forms/icheck/icheck.min.js')}}"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
	<div class="nk-content-body">
	    <div class="components-preview wide-md mx-auto">
	      <div class="nk-block nk-block-lg">
	        <div class="nk-block-head">
	          <div class="nk-block-head-content">
	            <h4 class="nk-block-title"><?php echo $jdl?></h4>
	          </div>
	        </div>
	        <div class="card card-preview">
	        	<div class="card-inner">
	        		<form id="frmEditor" class="form-horizontal" method="post" action="" enctype="multipart/form-data">
	        			@csrf
	        			<div class="col-md-12">
							<div class="row">
								<div class="form-group col-4">
									<label class="form-label" for="type">Content Type</label>
									<div class="form-control-wrap">
										<div class="i-checks" style="margin: 5px;">
											<input type="radio" name="content_type" id="news" value="news" checked>
											<label for="news"> News</label> &nbsp;&nbsp;
											<input type="radio" name="content_type" id="promo" value="promo">
											<label for="promo"> Promo</label>
										</div>
									</div>
								</div>
								<div class="form-group col-4">
									<label class="form-label" for="start_date">Start Date</label>
									<div class="form-control-wrap">
										<div class="form-icon form-icon-left">
											<em class="icon ni ni-calendar"></em>
										</div>
										<input type="text"
											id="start_date"
											name="start_date"
											class="form-control date-picker"
											data-date-format="dd/mm/yyyy"
											value="{{ date('d/m/Y') }}"
											autocomplete="off">
									</div>
								</div>
								<div class="form-group col-4">
									<label class="form-label" for="end_date">End Date</label>
									<div class="form-control-wrap">
										<div class="form-icon form-icon-left">
											<em class="icon ni ni-calendar"></em>
										</div>
										<input type="text"
											id="end_date"
											name="end_date"
											class="form-control date-picker"
											data-date-format="dd/mm/yyyy"
											value="{{ date('d/m/Y') }}"
											autocomplete="off">
									</div>
								</div>
							</div>
							
	        				<div class="form-group">
	        					<label for="news_title" class="col-xs-2 form-label">Title</label>
	        					<div class="col-xs-8">
	        						<input type="text" class="form-control" name="news_title" id="news_title" placeholder="Title" maxlength="160" onkeyup="hitungLength()">
	        						<p align="right"><span id="news_length">0</span>/160</p>
	        					</div>
	        				</div>
	        				<div class="form-group">
	        					<label for="news_descs" class="col-xs-2 form-label">Content</label>
	        					<div class="col-xs-8">
	        						<textarea class="form-control" name="news_descs" id="news_descs" placeholder="Place content newsfeed here" cols="30" rows="15" class="ckeditor"></textarea>
	        					</div>
	        				</div>
	        				<div class="form-group row" hidden>
	        					<div class="col-6">
	        						<label class="form-label">Start Period</label>
	        						<div class="form-control-wrap">
                                        <div class="form-icon form-icon-left">
                                            <em class="icon ni ni-calendar"></em>
                                        </div>
                                        <input type="text" id="start_date" name="start_date" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="<?php $mydate=date("d/m/Y");echo "$mydate";?>" >
                                    </div>
	        					</div>
	        					<div class="col-6">
	        						<label class="form-label">End Period</label>
	        						<div class="form-control-wrap">
                                        <div class="form-icon form-icon-left">
                                            <em class="icon ni ni-calendar"></em>
                                        </div>
                                        <input type="text" id="end_date" name="end_date" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="<?php $mydate=date("d/m/Y");echo "$mydate";?>" >
                                    </div>
	        					</div>
	        				</div>
	        				<div class="form-group row">
								<div class="col-6">
									<div class="form-group">
										<label class="form-label" for="type">Attachment Type</label>
										<div class="form-control-wrap">
											<div class="i-checks" style="margin: 5px;">
												<input type="radio" name="attach_type" id="type-P" value="P" checked>
												<label for="type-P"> Picture</label> &nbsp;&nbsp;
												<input type="radio" name="attach_type" id="type-Y" value="Y">
												<label for="type-Y"> Youtube</label>
											</div>
										</div>
									</div>
								</div>
	        					
	        					<div class="col-6">
									<div id="picture">
										<label for="upload" class="form-label">Upload Picture</label>
										<div class="form-control-wrap">
											<p ><img src="{{  url('images/PlProject/no_image.png') }}" id="picturebox" class="img-responsive" width="50%"></p>
											<div class="custom-file">
												
												<input type="file" id="userfile" name="userfile" class="custom-file-input" accept="image/*" >
												<label class="custom-file-label" for="userfile" id="pictname">Choose File</label>
												<p style="color: red">(* Only JPG, JPEG, PNG, GIF allowed)</p>
												<p style="color: red">
													Recommended Resolution: 640 × 480 pixels to ensure the image remains clear and does not appear pixelated.
												</p>
											</div>
										</div>
									</div>
									<div id="youtube">
										<label for="upload" class="form-label">Youtube Link</label>
										<div class="form-control-wrap">
											<div class="custom-file">
												<input type="text" id="youtubelink" name="youtubelink" class="form-control">
											</div>
										</div>
									</div>
	        					</div>
	        				</div>
	        				<div class="form-group" hidden>
	        					<div class="col-xs-4">
	        						<img src="" id="picturebox" class="img-responsive">
	        						<input type="text" class="form-control" name="picturepath" id="picturepath" value="" readonly><input type="text" class="form-control" name="picturename" id="picturename" readonly>
	        					</div>
				            </div>
	        			</div>
	        			<div style="text-align:right;margin-right: 50px;margin-top: 20px">
	        				<button type="button" id="btnSave" class="btn btn-primary">Save</button>
		                    <button type="button" class="btn btn-secondary" id="btnBack">Back</button>
		                </div>
	        		</form>
		        </div>
	        </div><!-- .card-preview -->
	      </div><!-- nk-block -->
	    </div>
	</div>
	<script type="text/javascript">
	function hitungLength()
	{
		var news = document.getElementById('news_title').value.length;
		document.getElementById('news_length').innerHTML = news;
	}

	let editorInstance; // <-- CKEditor 5 instance

	$(document).ready(function(){

		// ================= CKEDITOR 5 =================
		function createEditor(content = '') {
			ClassicEditor
				.create(document.querySelector('#news_descs'), {
					toolbar: [
						'undo', 'redo',
						'|',
						'heading',
						'|',
						'bold', 'italic', 'underline', 'strikethrough',
						'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor',
						'|',
						'link', 'insertTable', 'uploadImage', 'blockQuote', 'codeBlock',
						'|',
						'bulletedList', 'numberedList', 'todoList',
						'|',
						'alignment',
						'|',
						'outdent', 'indent',
						'|',
						'removeFormat'
					]
				})
				.then(editor => {
					editorInstance = editor;
					if (content) {
						editor.setData(content);
					}
				})
				.catch(error => {
					console.error(error);
				});
		}

		// init editor pertama
		createEditor();
		// =================================================

		// menampilkan data
		loaddata();

		$('.i-checks').iCheck({
			radioClass: 'iradio_square-blue',
			checkboxClass: 'icheckbox_flat-blue'
		});

		// button back
		$('#btnBack').click(function()
		{
			window.location.href="{{url('admin/news')}}";
	    });

		$('input[type=radio][name=attach_type]').on('ifChanged', function() {
			if (this.value == 'Y') {
				$("#youtube").show()
				$("#picture").hide()
				$("#picture").val('')
			}
			else if (this.value == 'P') {
				$("#youtube").hide()
				$("#youtubelink").val('')
				$("#picture").show()
			}
		});
		$("#youtube").hide()
		$("#picture").show()

		$('#end_date').change(function(){
			$(this).valid();
		});

	    $("#userfile").on('change', function ()
	    {
            $.ajaxSetup({
		        headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        }
		    });

            $.ajax({
            	url : "{{url('admin/news/savepic')}}",
            	type:"POST",
            	data: function () {
            		var data = new FormData();
		            data.append("userfile", $("#userfile").get(0).files[0]);
		            return data;
		        }(),
		        processData: false,
		        contentType: false,
		        dataType:"json",
		        success:function(data, status){
		            if(data.status == "OK"){
						$('#picturebox').attr('src', data.url);
						$('#picturepath').val(data.url)
						$('#picturename').val(data.picname)
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

		// ================= SAVE =================
	    $('#btnSave').click(function(event)
	    {
	    	event.preventDefault();
	    	if (event.handled !== true) {
	    		event.handled = true;
	    		if ($('#frmEditor').valid())
	    		{
	    			var id = '<?php echo $id?>';
	    			var action ='<?php echo $form?>';

	    			// ===== CKEDITOR 5 GET DATA =====
	    			var content = editorInstance.getData();

	    			var datafrm = $('#frmEditor').serializeArray();
		    			datafrm.push(
		    				{name:"action",value:action},
		    				{name:"id",value:id},
		    				{name:"news_descs",value:content},
							{ 
								name: 'start_date',
    							value: $('#start_date').val()
							},
							{ 
								name: 'end_date',
    							value: $('#end_date').val()
							}
		    			);
						console.log(datafrm);
	    			$.ajax({
	    				url : "{{ url('/admin/news/save') }}",
	    				type:"POST",
	    				data: datafrm,
	    				dataType:"json",
	    				success:function(event, data)
	    				{
	    					if (event.status == 'OK')
	    					{
	    						Swal.fire({
	    							title: "Information",
	    							icon:"success",
	    							text: event.pesan,
	    							confirmButtonText: "OK"
	    						}).then(function(){
	    							window.location.href="{{url('/admin/news')}}";
	  	    					});
	    					} else {
	  							Swal.fire({
	    							title: "Information",
	    							icon:"error",
	    							text: event.pesan,
	    							confirmButtonText: "OK"
	    						});
	    					}
	    				},
	    				error: function(jqXHR, textStatus, errorThrown){
	    					Swal.fire({
	    						title: "Error",
	    						icon:"error",
	    						text: textStatus+' Save : '+errorThrown,
	    						confirmButtonText: "OK",
	    					});
	    				}
	    			});
	    		}
	    	}
	    });

	    // ================= LOAD DATA =================
	    function loaddata(){
			var rowID = '<?php echo $id ?>';

		    if (rowID > 0)
		    {
		    	$.getJSON("{{url('/admin/news/id')}}" + "/" + rowID, function (data)
		    	{
					$('#'+data[0].content_type).iCheck('check');
					$('#type-'+data[0].attach_type).iCheck('check');
					$('#status-'+data[0].status).iCheck('check');
					$('#youtubelink').val(data[0].youtube_link);

		    		$('#news_title').val(data[0].subject);
		    		document.getElementById('news_length').innerHTML = document.getElementById('news_title').value.length;

		    		if(data[0].picture!="")
	                {
	                	$('#picturebox').attr("src",data[0].picture);
	                	$('#picturepath').val(data[0].picture);
	                }

		    		// ===== CKEDITOR 5 SET DATA =====
		    		if (editorInstance) {
		    			editorInstance.setData(data[0].content);
		    		}
		        });
		    }
		}
	});
</script>
@endsection
