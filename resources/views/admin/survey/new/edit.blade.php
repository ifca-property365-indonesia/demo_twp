<style>
  #formSurveyEdit label.error { margin-left: 10px; width: auto; display: inline; color: #a94442; }
  .has-error .form-control { border: 1px solid #a94442; }
  .question-block { border: 1px solid #e5e9f2; padding: 15px; margin-bottom: 15px; border-radius: 4px; background-color: #f8f9fa; }
</style>

<div class="ibox-content">
  <form role="form" class="form-horizontal" id="formSurveyEdit" method="POST">
    @csrf
    <input type="hidden" name="survey_id" value="{{ $survey->id }}">

    <div class="form-group">
      <label>Survey Title <span class="text-danger">*</span></label>
      <div class="col-12">
        <input type="text" class="form-control" name="title" id="title" value="{{ $survey->title }}" required>
      </div>
    </div>
    <hr>
    
    <div id="question-container">
      @foreach($questions as $index => $q)
      <div class="question-block" id="qb-{{ $index }}">
        <div class="form-group">
          <label>Question <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="questions[{{ $index }}][text]" value="{{ $q->question_text }}" required>
        </div>
        
        <div class="form-group">
          <label>Question Type</label>
          <select class="form-control" name="questions[{{ $index }}][type]" onchange="toggleOptions(this, {{ $index }})">
            <option value="multiple_choice" {{ $q->question_type == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
            <option value="essay" {{ $q->question_type == 'essay' ? 'selected' : '' }}>Questionnaire</option>
          </select>
        </div>

        <div id="options-area-{{ $index }}" style="{{ $q->question_type == 'essay' ? 'display:none;' : '' }}">
          <label>Answer Options <span class="text-danger">*</span></label>
          <div class="more-options-{{ $index }}">
            
            @if($q->question_type == 'multiple_choice' && count($q->options) > 0)
              @foreach($q->options as $optIndex => $opt)
              <div class="input-group mb-2">
                <input type="text" class="form-control" name="questions[{{ $index }}][options][]" value="{{ $opt->option_text }}" {{ $q->question_type == 'essay' ? 'disabled' : 'required' }}>
                <div class="input-group-append">
                  @if($optIndex == 0)
                    <button type="button" class="btn btn-success" onclick="addOption({{ $index }})"><em class="icon ni ni-plus"></em></button>
                  @else
                    <button type="button" class="btn btn-danger" onclick="$(this).closest('.input-group').remove()"><em class="icon ni ni-minus"></em></button>
                  @endif
                </div>
              </div>
              @endforeach
            @else
              <div class="input-group mb-2">
                <input type="text" class="form-control" name="questions[{{ $index }}][options][]" placeholder="Option 1" {{ $q->question_type == 'essay' ? 'disabled' : 'required' }}>
                <div class="input-group-append">
                  <button type="button" class="btn btn-success" onclick="addOption({{ $index }})"><em class="icon ni ni-plus"></em></button>
                </div>
              </div>
            @endif

          </div>
        </div>

        <div class="text-right mt-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="$('#qb-{{ $index }}').remove()">
                <em class="icon ni ni-trash"></em> Delete Question
            </button>
        </div>
      </div>
      @endforeach
    </div>

    <div class="form-group">
      <div class="col-12">
        <button type="button" class="btn btn-info btn-sm" onclick="addQuestion()">
          <em class="icon ni ni-plus-circle"></em> Add Question
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
      errorElement: "span",
      highlight: function (element, errorClass, validClass) {
        $(element).addClass(errorClass);
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass(errorClass);
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
      },
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
        <div class="form-group">
          <label>Question <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="questions[${qIndex}][text]" placeholder="Enter question..." required>
        </div>
        <div class="form-group">
          <label>Question Type</label>
          <select class="form-control" name="questions[${qIndex}][type]" onchange="toggleOptions(this, ${qIndex})">
            <option value="multiple_choice">Multiple Choice</option>
            <option value="essay">Questionnaire</option>
          </select>
        </div>
        <div id="options-area-${qIndex}">
          <label>Answer Options <span class="text-danger">*</span></label>
          <div class="more-options-${qIndex}">
            <div class="input-group mb-2">
              <input type="text" class="form-control" name="questions[${qIndex}][options][]" placeholder="Option 1" required>
              <div class="input-group-append">
                <button type="button" class="btn btn-success" onclick="addOption(${qIndex})"><em class="icon ni ni-plus"></em></button>
              </div>
            </div>
          </div>
        </div>
        <div class="text-right mt-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="$('#qb-${qIndex}').remove()">
                <em class="icon ni ni-trash"></em> Delete Question
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
        <div class="input-group-append">
          <button type="button" class="btn btn-danger" onclick="$(this).closest('.input-group').remove()"><em class="icon ni ni-minus"></em></button>
        </div>
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