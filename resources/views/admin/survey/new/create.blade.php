<style>

  .question-block {
    border: 1px solid #e5e9f2;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 4px;
    background-color: #f8f9fa;
  }
</style>

<div>
  <form role="form" id="formSurvey" method="POST">
    @csrf
    <!-- Header Survey -->
    <div class="mb-3">
      <label class="form-label">{{ __('admin/survey.survey_title') }} <span class="text-danger">*</span></label>
      <div class="col-12">
        <input type="text" class="form-control" name="title" id="title" placeholder="{{ __('admin/survey.input_survey_title') }}" required>
      </div>
    </div>

    <hr>
    
    <!-- Dynamic Question Area -->
    <div id="question-container">
      <!-- Questions will be injected here via JavaScript -->
    </div>

    <!-- Add Question Button -->
    <div class="mb-3">
      <div class="col-12">
        <button type="button" class="btn btn-info btn-sm" onclick="addQuestion()">
          <i class="cil-plus"></i> {{ __('admin/survey.add_question') }}
        </button>
      </div>
    </div>
  </form>
</div>

<script type="text/javascript">
  let qIndex = 0;

  $(document).ready(function(){
    // Turn off modal block UI when loading questionnaire
    if (typeof block === "function") {
        block(false, '#modalbodyxl');
    }
    
    // Re-enable save button
    $('#modalxl #savefrmxl').attr("disabled", false); 

    // Automatically spawn 1 question form on first open
    addQuestion();

    // jQuery Validate Configuration
    $('#formSurvey').validate({
      ignore: ":hidden",
      rules: {
        title: { required: true }
      },
      errorElement: "div",
      errorClass: "invalid-feedback",
      highlight: function (element) { $(element).addClass('is-invalid'); },
      unhighlight: function (element) { $(element).removeClass('is-invalid'); },
      errorPlacement: function (error, element) {
        if (element.hasClass('select2')) { error.insertAfter(element.next('.select2-container')); }
        else if (element.parent('.input-group').length) { error.insertAfter(element.parent()).addClass('d-block'); }
        else { error.insertAfter(element); }
      }
    });

    // Submit Action (Tied with Save button in Modal Footer)
    $('#modalxl #savefrmxl').unbind().click(function(e){
      e.preventDefault();
      $('#modalxl #savefrmxl').attr("disabled", true); 
      
      if (typeof block === "function") block(true, '#formSurvey');

      if($('#formSurvey').valid()){
        var datafrm = $('#formSurvey').serialize();
        
        $.ajax({
          url : "{{ url('admin/usersurvey/store') }}",
          type:"POST",
          data: datafrm,
          dataType:"json",
          success:function(data, status){
            if(data.status == 'OK'){
              Swal.fire({
                title: @json(__('common.information')),
                animation: false,
                icon: "success",
                text: data.message || data.pesan,
                confirmButtonText: @json(__('common.ok'))
              });
              $('#modalxl').modal('hide');
              
              // Reload table in parent view
              if (typeof tbldraft !== 'undefined') tbldraft.ajax.reload(null, true);  
              
              if (typeof block === "function") block(false, '#formSurvey');
              $('#modalxl #savefrmxl').attr("disabled", false); 
            } else {
              Swal.fire({
                title: @json(__('common.information')),
                animation: false,
                icon: "error",
                text: data.message || data.pesan,
                confirmButtonText: @json(__('common.ok'))
              });
              if (typeof block === "function") block(false, '#formSurvey');
              $('#modalxl #savefrmxl').attr("disabled", false);  
            }
          },                    
          error: function(jqXHR, textStatus, errorThrown){
            Swal.fire(@json(__('admin/survey.save_error')).replace(':status', textStatus).replace(':error', errorThrown), "", "error");
            if (typeof block === "function") block(false, '#formSurvey');
            $('#modalxl #savefrmxl').attr("disabled", false); 
          }
        });
      } else {
        if (typeof block === "function") block(false, '#formSurvey');
        $('#modalxl #savefrmxl').attr("disabled", false); 
      }
    });
  });

  // --- DYNAMIC FUNCTIONS FOR QUESTIONS & OPTIONS ---
  
  function addQuestion() {
    let html = `
      <div class="question-block" id="qb-${qIndex}">
        <div class="mb-3">
          <label class="form-label">{{ __('admin/survey.question') }} <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="questions[${qIndex}][text]" placeholder="{{ __('admin/survey.enter_question') }}" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label">{{ __('admin/survey.question_type') }}</label>
          <select class="form-select" name="questions[${qIndex}][type]" onchange="toggleOptions(this, ${qIndex})">
            <option value="multiple_choice">{{ __('admin/survey.multiple_choice') }}</option>
            <option value="essay">{{ __('admin/survey.questionnaire') }}</option>
          </select>
        </div>

        <div id="options-area-${qIndex}">
          <label class="form-label">{{ __('admin/survey.answer_options') }} <span class="text-danger">*</span></label>
          <div class="more-options-${qIndex}">
            <!-- Option form using Bootstrap Input Group -->
            <div class="input-group mb-2">
              <input type="text" class="form-control" name="questions[${qIndex}][options][]" placeholder="{{ __('admin/survey.option_n', ['number' => 1]) }}" required>
              <button type="button" class="btn btn-sm btn-success" onclick="addOption(${qIndex})"><i class="cil-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="text-end mt-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="$('#qb-${qIndex}').remove()">
                <i class="cil-trash"></i> {{ __('admin/survey.delete_question') }}
            </button>
        </div>
      </div>
    `;
    $('#question-container').append(html);
    qIndex++;
  }

  function addOption(index) {
    let optHtml = `
      <div class="input-group mb-2">
        <input type="text" class="form-control" name="questions[${index}][options][]" placeholder="{{ __('admin/survey.next_option') }}" required>
        <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('.input-group').remove()"><i class="cil-minus"></i></button>
      </div>`;
    $(`.more-options-${index}`).append(optHtml);
  }
  
  function toggleOptions(selectElement, index) {
      if (selectElement.value === 'essay') {
          $(`#options-area-${index}`).slideUp();
          // Disable option inputs and remove required attribute to pass validation
          $(`#options-area-${index} input`).prop('disabled', true).removeAttr('required');
      } else {
          $(`#options-area-${index}`).slideDown();
          // Enable option inputs and restore required attribute
          $(`#options-area-${index} input`).prop('disabled', false).attr('required', true);
      }
  }
</script>