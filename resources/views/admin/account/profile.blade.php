
        <div class="row">
            <div class="col-md-6">
                    <ul class="nav nav-tabs mt-n3">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" id="baseVerticalLeft2-tab1" href="#tabVerticalLeft21" aria-controls="tabVerticalLeft21" aria-selected="true"><em class="icon ni ni-user"></em><span>Personal</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" id="baseVerticalLeft2-tab2" href="#tabVerticalLeft22" aria-controls="tabVerticalLeft22" aria-selected="false"><em class="icon ni ni-lock-alt"></em><span>Password</span></a>
                        </li>
                    </ul>
                
                    <div class="row p-2">
                        <div class="form-group">
                            <label class="form-label" for="coname">Picture Profile</label><br>
                            <img id="picturebox" class="img-thumbnail mb-2 img-fluid w-90 pictured" src="https://i0.wp.com/www.winhelponline.com/blog/wp-content/uploads/2017/12/user.png?resize=256%2C256&quality=100&ssl=1" itemprop="thumbnail" alt="Image description">
                                <input type="file" id="userfile" name="userfile" accept="image/x-png,image/gif,image/jpeg"/>
                                <p>(* Only Jpg, Png allowed. Max 300kb)</p>
                            <input type="hidden" name="image" id="image" value="https://i0.wp.com/www.winhelponline.com/blog/wp-content/uploads/2017/12/user.png?resize=256%2C256&quality=100&ssl=1">
                            <input type="hidden" name="labelimage" id="labelimage">
                        </div>
                    </div>
            </div>
            
            <div class="col-md-6">
                <div class="tab-content col-md-12">
                    <div role="tabpanel" class="tab-pane active" id="tabVerticalLeft21" aria-expanded="true" aria-labelledby="baseVerticalLeft2-tab1">
                        <form id ="frmEditor" class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                            {{ csrf_field() }}
                        <h4> Personal Information</h4>
                        <div class="form-group">
                            <label for="name" class="form-label">Name <FONT COLOR="RED">*</FONT></label>
                            <div class="col-md-12">
                            <input type="text" class="form-control" id="name" name="name" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email <FONT COLOR="RED">*</FONT></label>
                            <div class="col-md-12">
                            <input type="text" class="form-control" id="email" name="email" readonly/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="handphone" class="form-label">Handphone <FONT COLOR="RED">*</FONT></label>
                            <div class="col-md-12">
                                <input type="text" class="form-control" id="handphone" name="handphone" />
                                Format: 6221995500 | 021995500
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnSave" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                        </div>
                        </form>
                    </div>
                
                    <div class="tab-pane" id="tabVerticalLeft22" aria-labelledby="baseVerticalLeft2-tab2">
                        <h4>Change Password</h4>
                        <form id ="frmchangepass" class="form-horizontal" method="post" action="">
                            <div class="form-group">
                                <label for="password" class="form-label">New Password <FONT COLOR="RED">*</FONT></label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control" id="password1" name="password1" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password" class="form-label">Confirm Password <FONT COLOR="RED">*</FONT></label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control" id="password2" name="password2" required />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="btnSavepass" class="btn btn-primary">Change</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

<script type="text/javascript">
    var isFile=false;
    var jqXHRData;
    loaddata();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    $(document).ready(function(){
        $.validator.addMethod("confirmpass", function (value, element) {
                var isSuccess = false;
                var newpassword = $('#password1').val();
                var confpassword = $('#password2').val();

                if(newpassword == confpassword){
                   isSuccess=true;
                }
                
                return isSuccess;
        });
        $("#frmEditor").validate({
            ignore:"",
            rules: {
                name: {
                    required: true
                },
                handphone:{
                    required:true
                },
            },
            messages: {
                errorElement: "span",
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass(errorClass); //.removeClass(errorClass);
                    $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass(errorClass); //.addClass(validClass);
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                },
                errorPlacement: function (error, element) {
                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else if (element.hasClass('select2')){
                        error.insertAfter(element.next('span'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            }
        });
        $("#frmchangepass").validate({
            ignore:"",
            rules: {
                password1: {
                    required: true//,
                    // confirmpass:true
                },
                password2:{
                    required:true,
                    confirmpass:true
                },
            },
           
            messages: {
                password2: {confirmpass: "Password is not valid"},
                errorElement: "span",
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass(errorClass); //.removeClass(errorClass);
                    $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass(errorClass); //.addClass(validClass);
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                },
                errorPlacement: function (error, element) {
                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else if (element.hasClass('select2')){
                        error.insertAfter(element.next('span'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            }
        });
    });
        
        $('#userfile').change(function(event) {
            event.preventDefault();
            event.handled = true;
            $.ajax({
            url : "{{url('admin/account/savepic')}}",
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
                    $('#image').val(data.url)
                    $('#labelimage').val(data.picname)
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
                Swal.fire('error',textStatus+' Save : '+errorThrown);
            }
            });
        });

        $('#btnSave').click(function(){
            var labelimage = $('#labelimage').val()
            if($('#frmEditor').valid()){
            var dataform = $('#frmEditor').serializeArray();
            dataform.push({name:"isFile",value:isFile},
                          {name:"labelimage",value:labelimage},
                        );
            var obj = new Object();
            obj.isFile = isFile;
                if(isFile){
                    if(jqXHRData){
                        jqXHRData.formData = dataform;
                        jqXHRData.submit();
                    }
                } else {
                    var site_url = "{{ url('admin/account/updateprofile') }}";
                    $.ajax({
                        url: site_url,
                        type: "POST",
                        data: dataform,
                        dataType: "json",
                        success: function(data, status){
                            if(status=='success'){
                                Swal.fire({
                                    title: "Information",
                                    text: data.pesan,
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then(function(){
                                    $('#modal').modal('hide');
                                });

                                // location.reload();
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
                            Swal.fire('error',textStatus+' Save : '+errorThrown);
                        }
                    });
                }
            }
        });

        $('#btnSavepass').click(function(){
            if($('#frmchangepass').valid()){
                var dataform = $('#frmchangepass').serializeArray();
                var email = $('#email').val();
                var password = $('#password2').val();
                var confpass  = $('#password1').val();
                if(password != confpass){
                    Swal.fire('Information', 'Password mismatch','error');
                    return;
                }
                dataform.push({name:"email",value:email},{name:"password",value:password})
                var site_url = "{{ url('admin/account/changepass') }}";
                $.ajax({
                    url: site_url,
                    type: "POST",
                    data: dataform,
                    dataType: "json",
                    success: function(data, status){
                        if(status=='success'){
                            Swal.fire({
                                title: "Information",
                                text: data.pesan,
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(function(){
                                $('#modal').modal('hide');
                            })
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
                        Swal.fire('error',textStatus+' Save : '+errorThrown);
                    }
                });
            }
        });

    function loaddata(){
        var Id = $('#modal').data('Id');
        if (Id.length > 0) {
            $.getJSON("{{ url('admin/account/getbyemail') }}" + "/" + Id, function (data) {
                $("#name").val(data[0].name);
                $("#handphone").val(data[0].handphone);
                $("#email").val(data[0].email);
                $('#image').val(data[0].pict);
                $('#labelimage').val(data[0].pict);
                var url = data[0].pict;
                if(url != "" || url != null)
                {
                    var filename = url.substring(url.lastIndexOf('/')+1);
                    $('#labelimage').text(filename);
                    $('.pictured').attr("src",url);
                }
            });
        }
    }
</script>
