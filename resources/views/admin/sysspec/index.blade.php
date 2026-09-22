@extends('admin.template.layout2.base')
@section('content')
<div class="page-body">
    <div>
        <div class="page-block">
            <div class="page-head">
                <div class="page-head-row">
                    <div class="page-head-content">
                        <h3 class="page-title">System Specification</h3>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab5" role="tablist">
                        <li class="nav-item">
                        <a class="nav-link active" id="base-tab1" data-coreui-toggle="tab" aria-controls="tab1" href="#adminimage" aria-expanded="true">
                            <i class="cil-building"></i> &nbsp; Admin Login Image</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" id="base-tab2" data-coreui-toggle="tab" aria-controls="tab2" href="#tenantimage" aria-expanded="false">
                            <i class="cil-puzzle"></i> &nbsp; Tenant Login Image</a>
                        </li>
                  
                    </ul>
                    <div class="tab-content" id="myTabContent5">
                        <div class="tab-pane fade show active" id="adminimage" role="tabpanel" aria-labelledby="home-tab5">
                            <div class="container">
                                <div class="row g-4">
                                    <div class="col-6 col-sm-4">
                                        <img id="picturebox1" class="img-thumbnail img-fluid w-100 pictured mb-2" src="<?php echo $image1; ?>" itemprop="thumbnail" alt="Image description">
                                        <input type="file" class="form-control form-control-sm" id="imglogin1" name="imglogin" accept="image/x-png,image/gif,image/jpeg" onChange="saveImage(1,this,'admin')"/>
                                        <p>(* Only Jpg, Png allowed)</p>
                                        <input type="hidden" name="namagambar" id="pathgambar1" value="<?php echo $image1; ?>">
                                        <input type="hidden" name="picname" id="picname1">
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <img id="picturebox2" class="img-thumbnail img-fluid w-100 pictured mb-2" src="<?php echo $image2; ?>" itemprop="thumbnail" alt="Image description">
                                        <input type="file" class="form-control form-control-sm" id="imglogin2" name="imglogin" accept="image/x-png,image/gif,image/jpeg" onChange="saveImage(2,this,'admin')"/>
                                        <p>(* Only Jpg, Png allowed)</p>
                                        <input type="hidden" name="namagambar" id="pathgambar2" value="<?php echo $image2; ?>">
                                        <input type="hidden" name="picname" id="picname2">
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <img id="picturebox3" class="img-thumbnail img-fluid w-100 pictured mb-2" src="<?php echo $image3; ?>" itemprop="thumbnail" alt="Image description">
                                        <input type="file" class="form-control form-control-sm" id="imglogin3" name="imglogin" accept="image/x-png,image/gif,image/jpeg" onChange="saveImage(3,this,'admin')"/>
                                        <p>(* Only Jpg, Png allowed)</p>
                                        <input type="hidden" name="namagambar" id="pathgambar3" value="<?php echo $image3; ?>">
                                        <input type="hidden" name="picname" id="picname3">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tenantimage" role="tabpanel" aria-labelledby="profile-tab5">
                            <div class="container">
                                <div class="row g-4">
                                    <div class="col-6 col-sm-4">
                                        <img id="picturebox4" class="img-thumbnail img-fluid w-100 pictured mb-2" src="<?php echo $image4; ?>" itemprop="thumbnail" alt="Image description">
                                        <input type="file" class="form-control form-control-sm" id="imglogin4" name="imglogin" accept="image/x-png,image/gif,image/jpeg" onChange="saveImage(4,this,'tenant')"/>
                                        <p>(* Only Jpg, Png allowed)</p>
                                        <input type="hidden" name="namagambar" id="pathgambar4" value="<?php echo $image1; ?>">
                                        <input type="hidden" name="picname" id="picname4">
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <img id="picturebox5" class="img-thumbnail img-fluid w-100 pictured mb-2" src="<?php echo $image5; ?>" itemprop="thumbnail" alt="Image description">
                                        <input type="file" class="form-control form-control-sm" id="imglogin5" name="imglogin" accept="image/x-png,image/gif,image/jpeg" onChange="saveImage(5,this,'tenant')"/>
                                        <p>(* Only Jpg, Png allowed)</p>
                                        <input type="hidden" name="namagambar" id="pathgambar5" value="<?php echo $image5; ?>">
                                        <input type="hidden" name="picname" id="picname5">
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <img id="picturebox6" class="img-thumbnail img-fluid w-100 pictured mb-2" src="<?php echo $image6; ?>" itemprop="thumbnail" alt="Image description">
                                        <input type="file" class="form-control form-control-sm" id="imglogin6" name="imglogin" accept="image/x-png,image/gif,image/jpeg" onChange="saveImage(6,this,'tenant')"/>
                                        <p>(* Only Jpg, Png allowed)</p>
                                        <input type="hidden" name="namagambar" id="pathgambar6" value="<?php echo $image6; ?>">
                                        <input type="hidden" name="picname" id="picname6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function saveImage(seq, el, web) {
        var a = el.files[0].size;
        var max = (1024 *1024) * 7;
        if (a > max){
            if (max.toString().length > 6) {
                max = max / 1024 / 1024;
                max = max.toFixed(2);
                max = max + ' mb';
            } else {
                max = max / 1024;
                max = max.toFixed(2);
                max = max + ' kb';
            }
            Swal.fire('Please upload less than ' + max);
            return false;
        }
 
        $.ajax({
            url : "{{ url('admin/systemspec/saveimage') }}",
            type:"POST",
            data: function () {
                var data = new FormData();
                data.append("web",web);
                data.append("seq",seq);
                data.append("imglogin", $("#imglogin"+seq).get(0).files[0]);
                data.append("namagambar", $("#pathgambar"+seq).val());
                return data;
            }(),
            processData: false,
            contentType: false,
            dataType:"json",
            success:function(data, status){
            if(data.status == "OK"){
                    Swal.fire({
                    title: "Information",
                    text: data.pesan,
                    icon: "success",
                    confirmButtonText: "OK"
                    });
                    console.log(data.url);
                    $('#picturebox'+seq).attr('src', data.url);
                    $('#pathgambar'+seq).val(data.picname);
                    $('#picname'+seq).val(data.url);
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
    }

</script>

@endsection
