<style>
  .question-block { border: 1px solid #e5e9f2; padding: 15px; margin-bottom: 15px; border-radius: 4px; background-color: #f8f9fa; }
</style>

<div>
  <form role="form" id="formSurveyEdit" method="POST">
    @csrf
    <input type="hidden" name="survey_id" value="{{ $survey->id }}">

    <div class="mb-3">
      <label class="form-label">Survey Title <span class="text-danger">*</span></label>
      <div class="col-12">
        <input type="text" class="form-control" name="title" id="title" value="{{ $survey->title }}" required>
      </div>
    </div>
    <hr>
    
    <div id="question-container">
      @foreach($questions as $index => $q)
      <div class="question-block" id="qb-{{ $index }}">
        <div class="mb-3">
          <label class="form-label">Question <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="questions[{{ $index }}][text]" value="{{ $q->question_text }}" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Question Type</label>
          <select class="form-select" name="questions[{{ $index }}][type]" onchange="toggleOptions(this, {{ $index }})">
            <option value="multiple_choice" {{ $q->question_type == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
            <option value="essay" {{ $q->question_type == 'essay' ? 'selected' : '' }}>Questionnaire</option>
          </select>
        </div>

        <div id="options-area-{{ $index }}" style="{{ $q->question_type == 'essay' ? 'display:none;' : '' }}">
          <label class="form-label">Answer Options <span class="text-danger">*</span></label>
          <div class="more-options-{{ $index }}">
            
            @if($q->question_type == 'multiple_choice' && count($q->options) > 0)
              @foreach($q->options as $optIndex => $opt)
              <div class="input-group mb-2">
                <input type="text" class="form-control" name="questions[{{ $index }}][options][]" value="{{ $opt->option_text }}" {{ $q->question_type == 'essay' ? 'disabled' : 'required' }}>
                @if($optIndex == 0)
                    <button type="button" class="btn btn-sm btn-success" onclick="addOption({{ $index }})"><i class="cil-plus"></i></button>
                  @else
                    <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('.input-group').remove()"><i class="cil-minus"></i></button>
                  @endif
              </div>
              @endforeach
            @else
              <div class="input-group mb-2">
                <input type="text" class="form-control" name="questions[{{ $index }}][options][]" placeholder="Option 1" {{ $q->question_type == 'essay' ? 'disabled' : 'required' }}>
                <button type="button" class="btn btn-sm btn-success" onclick="addOption({{ $index }})"><i class="cil-plus"></i></button>
              </div>
            @endif

          </div>
        </div>

        <div class="text-end mt-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="$('#qb-{{ $index }}').remove()">
                <i class="cil-trash"></i> Delete Question
            </button>
        </div>
      </div>
      @endforeach
    </div>

    <div class="mb-3">
      <div class="col-12">
        <button type="button" class="btn btn-info btn-sm" onclick="addQuestion()">
          <i class="cil-plus"></i> Add Question
        </button>
      </div>
    </div>
  </form>
</div>

<script type="text/javascript">
  let qIndex = {{ count($questions) }};

  $(document).ready(function(){
    if (typeof block === "function") block(false, '#modalbodyxl');
    $('#modalxl #savefrmxl').attr("disabled", false); 

    $('#formSurveyEdit').validate({
      ignore: ":hidden",
      rules: { title: { required: true } },
      errorElement: "div",
      errorClass: "invalid-feedback",
      highlight: function (element) { $(element).addClass('is-invalid'); },
      unhighlight: function (element) { $(element).removeClass('is-invalid'); },
      errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) error.insertAfter(element.parent());
        else error.insertAfter(element);
      }
    });

    $('#modalxl #savefrmxl').unbind().click(function(e){
      e.preventDefault();
      $('#modalxl #savefrmxl').attr("disabled", true); 
      if (typeof block === "function") block(true, '#formSurveyEdit');

      if($('#formSurveyEdit').valid()){
        var datafrm = $('#formSurveyEdit').serialize(); 
        
        $.ajax({
          url : "{{ url('admin/usersurvey/update') }}",
          type:"POST",
          data: datafrm,
          dataType:"json",
          success:function(data, status){
            if(data.status == 'OK'){
              Swal.fire({ title: "Information", animation: false, icon: "success", text: data.message, confirmButtonText: "OK" });
              $('#modalxl').modal('hide');
              if (typeof tbldraft !== 'undefined') tbldraft.ajax.reload(null, false);  
            } else {
              Swal.fire({ title: "Information", animation: false, icon: "error", text: data.message, confirmButtonText: "OK" });
            }
            if (typeof block === "function") block(false, '#formSurveyEdit');
            $('#modalxl #savefrmxl').attr("disabled", false); 
          },                    
          error: function(jqXHR, textStatus, errorThrown){
            Swal.fire("Save Error: " + textStatus + " - " + errorThrown, "", "error");
            if (typeof block === "function") block(false, '#formSurveyEdit');
            $('#modalxl #savefrmxl').attr("disabled", false); 
          }
        });
      } else {
        if (typeof block === "function") block(false, '#formSurveyEdit');
        $('#modalxl #savefrmxl').attr("disabled", false); 
      }
    });
  });

  function addQuestion() {
    let html = `
      <div class="question-block" id="qb-${qIndex}">
        <div class="mb-3">
          <label class="form-label">Question <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="questions[${qIndex}][text]" placeholder="Enter question..." required>
        </div>
        <div class="mb-3">
          <label class="form-label">Question Type</label>
          <select class="form-select" name="questions[${qIndex}][type]" onchange="toggleOptions(this, ${qIndex})">
            <option value="multiple_choice">Multiple Choice</option>
            <option value="essay">Questionnaire</option>
          </select>
        </div>
        <div id="options-area-${qIndex}">
          <label class="form-label">Answer Options <span class="text-danger">*</span></label>
          <div class="more-options-${qIndex}">
            <div class="input-group mb-2">
              <input type="text" class="form-control" name="questions[${qIndex}][options][]" placeholder="Option 1" required>
              <button type="button" class="btn btn-sm btn-success" onclick="addOption(${qIndex})"><i class="cil-plus"></i></button>
            </div>
          </div>
        </div>
        <div class="text-end mt-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="$('#qb-${qIndex}').remove()">
                <i class="cil-trash"></i> Delete Question
            </button>
        </div>
      </div>`;
    $('#question-container').append(html);
    qIndex++;
  }

  function addOption(index) {
    let optHtml = `
      <div class="input-group mb-2">
        <input type="text" class="form-control" name="questions[${index}][options][]" placeholder="Next option..." required>
        <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('.input-group').remove()"><i class="cil-minus"></i></button>
      </div>`;
    $(`.more-options-${index}`).append(optHtml);
  }

  function toggleOptions(selectElement, index) {
      if (selectElement.value === 'essay') {
          $(`#options-area-${index}`).slideUp();
          $(`#options-area-${index} input`).prop('disabled', true).removeAttr('required');
      } else {
          $(`#options-area-${index}`).slideDown();
          $(`#options-area-${index} input`).prop('disabled', false).attr('required', true);
      }
  }
</script>