<div>
    <form role="form" enctype="multipart/form-data" id="form_publish" method="POST" >
      <div class="mb-3">
        <label class="form-label">{{ __('admin/survey.survey_title') }}</label>
        <div class="col-12">
          <input type="text" class="form-control" name="txttitle" id="txttitle" placeholder="{{ __('admin/survey.input_subject') }}" readonly>
        </div>
      </div>
      <div class="mb-3" >
        <label class="form-label">{{ __('admin/survey.publish_date') }}</label>
        <div class="form-control-wrap">
            <div class="form-icon form-icon-left">
                <i class="cil-calendar"></i>
            </div>
            <input type="text" id="txtPublish" name="txtPublish" placeholder="{{ __('admin/survey.publish_date') }}" class="form-control date-picker" data-date-format="dd/mm/yyyy" >
        </div>
        
      </div>
      <div class="mb-3">
        <label class="form-label">{{ __('admin/survey.expired_date') }}</label>
        <div class="form-control-wrap">
            <div class="form-icon form-icon-left">
                <i class="cil-calendar"></i>
            </div>
            <input type="text" id="txtExpired" name="txtExpired" placeholder="{{ __('admin/survey.expired_date') }}" class="form-control date-picker" data-date-format="dd/mm/yyyy" >
        </div>
      </div>
    </form>
  </div>         
<script type="text/javascript"> 
loaddata(); 
function loaddata(){
  var form = $('#modalxl').data('form');
  var publish_id = $('#modalxl').data('id');
  var title = $('#modalxl').data('title');
  $("#txttitle").val(title);
} 
    $('.date-picker').datepicker({ autoclose: true,startDate: new Date()});
    $('#form_publish').validate({
      ignore: "",
      rules: {
        txttitle: { required: true},
        txtPublish: {required: true},
        txtExpired: {
                      required: true,
                      cek_date:true
                    },
      },
      messages: {
        txtExpired:{
                    cek_date:@json(__('admin/survey.expired_smaller_publish'))
                  }
              },
      errorElement: "div",
      errorClass: "invalid-feedback",
      highlight: function (element) { $(element).addClass('is-invalid'); },
      unhighlight: function (element) { $(element).removeClass('is-invalid'); },
        errorPlacement: function (error, element) {
          if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
          } else if (element.hasClass('select2_demo_1') || element.hasClass('select2_demo_2')) {
            error.insertAfter(element.next('span'));
          } else {
            error.insertAfter(element);
          }
        }

    });
$('#txtExpired').change(function(){
    $(this).valid();
});
$('#txtPublish').change(function(){
    $(this).valid();
});
$.validator.addMethod("cek_date", function (value, element) {
    var isSuccess = false;
    var endperiod = $('#txtExpired').val().split("/");
    expired = new Date(endperiod[2], endperiod[1] - 1, endperiod[0]);
    var publish = $('#txtPublish').val().split("/");
    published = new Date(publish[2], publish[1] - 1, publish[0]);
    console.log($('#txtPublish').val());
    if(expired < published){

    }
    else{
        isSuccess=true;
    }

    return isSuccess;
});
$('#modalxl #savefrm_publish').click(function(){
      if($('#form_publish').valid()){
        
        var publish_id = $('#modalxl').data('id');
        var form = $('#modalxl').data('form');
        var datafrm = $('#form_publish').serializeArray();
        var a1 = $('#txtPublish').val();
        var date = new Date(parseInt(a1.substr(0,10)));
        var year =a1.substr(6,4);
        var month=a1.substr(3,2);//tuker
        var day =a1.substr(0,2);//tuker
                               
        var aa1 = year+"-"+month+"-"+day;
        var b ="";
        var publish = "";
        if(aa1 == "--"){
           publish =  b;
        } else {
           publish =  aa1;
        }
                 
        var a2 = $('#txtExpired').val();
        var date = new Date(parseInt(a2.substr(0,10)));
        var year =a2.substr(6,4);
        var month=a2.substr(3,2);
        var day =a2.substr(0,2);
        var a1a1 = year+"-"+month+"-"+day;
        var b ="";
        var expired = "";
        if(a1a1 == "--"){
          expired =  b;
        } else {
           expired =  a1a1;
        }

        datafrm.push(
            {name:"publishDate",value:publish},
            {name:"ExpiredDate",value:expired},
            {name:"publish_id",value:publish_id},
            {name:"form",value:form}
        );
        block(true,'#form_publish');
            
            $.ajax({
                url : "{{ url('admin/survey/publish/savepublish')}}",
                type:"POST",
                data: datafrm,
                dataType:"json",
                success:function(data, status){
                if(data.status =='OK'){
                  Swal.fire({
                          title: @json(__('common.information')),
                          animation: false,
                          icon: "success",
                          text: data.pesan,
                          confirmButtonText: @json(__('common.ok'))
                      });
                      $('#modalxl').modal('hide');
                      tblsurvey.ajax.reload(null,true);  
                      tblpublishedd.ajax.reload(null,true);  
                      block(false,'#form_publish');
                      $('#modalxl #savefrm_publish').attr("disabled", false); 
                }else{
                  Swal.fire({
                            title: @json(__('common.information')),
                            animation: false,
                            icon: "error",
                            text: data.pesan,
                            confirmButtonText: @json(__('common.ok'))
                        });
                      block(false,'#form_publish');
                      $('#modalxl #savefrm_publish').attr("disabled", false); 
                  }
                  block(false,'#form_publish');
               
              
              },                    
                error: function(jqXHR, textStatus, errorThrown){
                  Swal.fire(@json(__('admin/survey.status_save_error')).replace(':status', textStatus).replace(':error', errorThrown));
                    block(false,'#form_publish');
                    $('#modalxl #savefrm_publish').attr("disabled", false); 
                }
            });
      }else{
        block(false,'#form_publish');
        $('#modalxl #savefrm_publish').attr("disabled", false); 
      }

});
</script>