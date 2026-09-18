@extends('tenant.template.base')
@section('content')
<div class="nk-content-body">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Take Survey</h3>
            </div>
        </div>
    </div>
    
    <div class="nk-block">
        @if(count($surveys) > 0)
            @foreach($surveys as $survey)
                <!-- SEPARATE CARD FOR EACH SURVEY ID -->
                <div class="card card-preview mb-4">
                    <div class="card-inner">
                        <form id="frm{{ $survey->id }}">
                            <input type="hidden" name="survey_id" value="{{ $survey->id }}">
                            <input type="hidden" name="email" value="{{ auth()->user()->email ?? 'user@domain.com' }}">

                            <!-- Survey Title & Description -->
                            <h4 class="survey-title text-primary border-bottom pb-2 mb-3">{{ $survey->title }}</h4>
                            @if($survey->description)
                                <p class="text-muted mb-4">{{ $survey->description }}</p>
                            @endif

                            <!-- Questions List -->
                            <div class="questions-container mt-3">
                                @foreach($survey->questions as $index => $q)
                                    <div class="form-group mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                                        <label class="form-label font-weight-bold text-dark">
                                            {{ $index + 1 }}. {{ $q->question_text }} <span class="text-danger">*</span>
                                        </label>
                                        
                                        <div class="form-control-wrap mt-2">
                                            @if($q->question_type == 'multiple_choice')
                                                <!-- Multiple Choice -->
                                                @foreach($q->options as $opt)
                                                    <div class="custom-control custom-radio custom-control-sm mb-2">
                                                        <!-- Tambahkan class 'opt-radio' dan 'data-qid' untuk trigger JS -->
                                                        <input type="radio" class="custom-control-input opt-radio" name="answers[{{ $q->id }}]" id="opt_{{ $opt->id }}" value="{{ $opt->id }}" data-qid="{{ $q->id }}" required>
                                                        <label class="custom-control-label" for="opt_{{ $opt->id }}">{{ $opt->option_text }}</label>
                                                    </div>
                                                @endforeach
                                                
                                                <!-- REMARKS INPUT (HIDDEN INITIALLY) -->
                                                <div class="remarks-box mt-2" id="remarks_box_{{ $q->id }}" style="display: none;">
                                                    <input type="text" class="form-control form-control-sm" name="remarks[{{ $q->id }}]" placeholder="Remarks (Optional)">
                                                </div>
                                            @else
                                                <!-- Questionnaire / Essay -->
                                                <textarea class="form-control" name="answers[{{ $q->id }}]" rows="3" placeholder="Type your answer here..." required></textarea>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Submit Button per Form -->
                            <div class="form-group mt-4 text-right">
                                <button type="button" id="btnSave{{ $survey->id }}" data-p="{{ $survey->id }}" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            <!-- PAGINATION -->
            <div class="d-flex justify-content-center mt-4">
                {{ $surveys->links('pagination::bootstrap-4') }}
            </div>

        @else
            <!-- Empty State -->
            <div class="card card-preview">
                <div class="card-inner">
                    <p class="card-text badge badge-gray mb-0">No Survey Available</p>
                </div>
            </div>
        @endif
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        // CSRF Token Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // TAMPILKAN REMARKS SAAT OPSI RADIO DIPILIH
        $(document).on('change', '.opt-radio', function() {
            var qid = $(this).data('qid');
            $('#remarks_box_' + qid).slideDown(); // Memunculkan input remarks secara smooth
        });

        // Handle submit button click for each form
        $(document).on('click', '[id^="btnSave"]', function(event) {
            event.preventDefault();
            
            var button = $(this);
            var publishId = button.data('p');
            var formTarget = $('#frm' + publishId);

            // Form Validation
            if (formTarget.valid()) {
                
                button.prop('disabled', true).html('Submitting...');
                var datafrm = formTarget.serializeArray();
                
                $.ajax({
                    url : "{{ url('/tenant/usersurvey/submit') }}",
                    type: "POST",
                    data: datafrm,
                    dataType: "json",
                    success: function(event) {
                        if (event.status == 'OK') {
                            Swal.fire({
                                title: "Information",
                                icon: "success",
                                text: event.message || event.pesan,
                                confirmButtonText: "OK"
                            }).then(function(){
                                window.location.reload(); 
                            });
                        } else {
                            Swal.fire({
                                title: "Information",
                                icon: "error",
                                text: event.message || event.pesan,
                                confirmButtonText: "OK"
                            });
                            button.prop('disabled', false).html('Submit');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        Swal.fire({
                            title: "Error",
                            icon: "error",
                            text: textStatus + ' Save : ' + errorThrown,
                            confirmButtonText: "OK",
                        });
                        button.prop('disabled', false).html('Submit');
                    }
                });
            }
        });
    });
</script>
@endsection