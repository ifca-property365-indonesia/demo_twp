
<div>
    <form role="form" enctype="multipart/form-data" id="form_publish" method="POST" >
      <div class="mb-3">
        <label class="form-label">{{ __('admin/survey.survey_title') }}</label>
        <div class="col-12">
          <input type="text" class="form-control" name="txttitle" id="txttitle" placeholder="{{ __('admin/survey.input_survey_title') }}">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">{{ __('admin/survey.subject') }}</label>
        <div class="col-12">
          <select name="txtsubject[]" id="txtsubject" data-placeholder="{{ __('admin/survey.select_subject') }}" style="width: 100%;" class="select2 form-control" tabindex="2" multiple="multiple">
            <option value=""></option>
            <?php echo $ddsubject;?>                
          </select>
        </div>
      </div>   
    </form>
  </div>            
  
  <script type="text/javascript">
  $(document).ready(function(){
    block(false,'#modalbodyxl');
  });
    loaddata();
    $('#savefrmxl').attr("disabled", false); 
    $("#txtsubject").select2();

      $('#form_publish').validate({
        ignore: "",
        rules: {
          txttitle: { required: true},
      
          txtsubject: {required: true},
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
  
    $('#modalxl #savefrmxl').unbind().click(function(){
      $('#modalxl #savefrmxl').attr("disabled", true); 
      block(true,'#form_publish');
        if($('#form_publish').valid()){
          var txttitle = $("#txttitle").val();
          var subject = $("#txtsubject").val();
          var publish_id = $('#modalxl').data('id');
          var form = $('#modalxl').data('form');
          var datafrm = $('#form_publish').serializeArray();
          var batas = subject.length;
          datafrm.push(
                       {name:"txttitle",value:txttitle}, 
                       {name:"publish_id",value:publish_id},
                       {name:"form",value:form},
                       {name:"batas",value:batas}
                    );
          $.ajax({
              url : "{{ url('admin/survey/publish/save') }}",
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
                    block(false,'#form_publish');
                    $('#modalxl #savefrmxl').attr("disabled", false); 
              }else{
                Swal.fire({
                          title: @json(__('common.information')),
                          animation: false,
                          icon: "error",
                          text: data.pesan,
                          confirmButtonText: @json(__('common.ok'))
                      });
                    block(false,'#form_publish');
                    $('#modalxl #savefrmxl').attr("disabled", false);  
                }
                
              },                    
              error: function(jqXHR, textStatus, errorThrown){
                Swal.fire(@json(__('admin/survey.status_save_error')).replace(':status', textStatus).replace(':error', errorThrown),"","error");
                  block(false,'#form_publish');
                  $('#modalxl #savefrmxl').attr("disabled", false); 
              }
            });
        }else{
          block(false,'#form_publish');
          $('#modalxl #savefrmxl').attr("disabled", false); 
        }
  
  
  });
  function loaddata(){
    var form = $('#modalxl').data('form');
    var publish_id = $('#modalxl').data('id');
    if (form=='edit') {
     
      $.getJSON("{{ url('admin/survey/publish/id') }}" + "/" + publish_id , function (data) {
            var dd = new Array();
            console.log(data);
            for(var i = 0; i<data.length; i++){
              dd[i] = data[i].tmpsurvey_id;
            }
            initSelect(dd);
            $('#txtsubject').val(dd).trigger("change");
            $("#txttitle").val(data[0].title);
  
          })
  
    }
  }
  function selectItem(target, id) { // refactored this a bit, don't pay attention to this being a function
    var option = $(target).children('[value='+id+']');
    option.detach();
    $(target).append(option).change();
  } 
  function initSelect(items) { // pre-select items
    items.forEach(item => { // iterate through array of items that need to be pre-selected
      let value = $('#txtsubject option[value='+item+']').text(); // get items inner text
      $('#txtsubject option[value='+item+']').remove(); // remove current item from DOM
      $('#txtsubject').append(new Option(value, item, true, true)); // append it, making it selected by default
    });
  }
      
      $('#modal').one('hidden.coreui.modal', function (e) {
          $('div.modal-body').html("");
          $(this).removeData();
      });
  
    </script> 
