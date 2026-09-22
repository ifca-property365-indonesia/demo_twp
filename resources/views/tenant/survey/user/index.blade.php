@extends('tenant.template.base')

@section('title', 'Online Survey')

@section('content')
<div class="page-body">
    <div class="page-head">
        <div class="page-head-row">
            <div class="page-head-content">
                <h3 class="page-title">Take Survey</h3>
                <div class="page-desc">Your feedback helps us improve our service.</div>
            </div>
        </div>
    </div>

    <div class="page-block">
        @if(count($surveys) > 0)
            @foreach($surveys as $survey)
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="frm{{ $survey->id }}" class="survey-form" novalidate>
                            <input type="hidden" name="survey_id" value="{{ $survey->id }}">
                            <input type="hidden" name="email" value="{{ auth()->user()->email ?? 'user@domain.com' }}">

                            <h4 class="text-primary border-bottom pb-2 mb-3">{{ $survey->title }}</h4>
                            @if($survey->description)
                                <p class="text-body-secondary mb-4">{{ $survey->description }}</p>
                            @endif

                            <div class="questions-container">
                                @foreach($survey->questions as $index => $q)
                                    <div class="mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                                        <label class="form-label fs-6 text-body">
                                            {{ $index + 1 }}. {{ $q->question_text }} <span class="text-danger">*</span>
                                        </label>

                                        @if($q->question_type == 'multiple_choice')
                                            @foreach($q->options as $opt)
                                                <div class="form-check mb-2">
                                                    <input type="radio" class="form-check-input opt-radio" name="answers[{{ $q->id }}]" id="opt_{{ $opt->id }}" value="{{ $opt->id }}" data-qid="{{ $q->id }}" required>
                                                    <label class="form-check-label" for="opt_{{ $opt->id }}">{{ $opt->option_text }}</label>
                                                </div>
                                            @endforeach
                                            <div class="remarks-box mt-2" id="remarks_box_{{ $q->id }}" style="display: none;">
                                                <input type="text" class="form-control form-control-sm" name="remarks[{{ $q->id }}]" placeholder="Remarks (optional)">
                                            </div>
                                        @else
                                            <textarea class="form-control" name="answers[{{ $q->id }}]" rows="3" placeholder="Type your answer here..." required></textarea>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="text-end">
                                <button type="button" id="btnSave{{ $survey->id }}" data-p="{{ $survey->id }}" class="btn btn-primary btn-save-survey">
                                    <i class="cil-send"></i><span>Submit</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            <div class="d-flex justify-content-center mt-4">
                {{ $surveys->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5 text-body-secondary">
                    <i class="cil-task fs-1 d-block mb-2"></i>
                    No survey available.
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(function () {
        // Tampilkan kotak remarks saat opsi dipilih
        $(document).on('change', '.opt-radio', function () {
            $('#remarks_box_' + $(this).data('qid')).slideDown();
        });

        $(document).on('click', '.btn-save-survey', function (event) {
            event.preventDefault();

            var button = $(this);
            var form = document.getElementById('frm' + button.data('p'));

            // Validasi HTML5 (required) tanpa plugin
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            button.prop('disabled', true).find('span').text('Submitting...');

            $.ajax({
                url: "{{ url('/tenant/usersurvey/submit') }}",
                type: 'POST',
                data: $(form).serializeArray(),
                dataType: 'json'
            }).done(function (res) {
                if (res.status == 'OK') {
                    Swal.fire({ title: 'Thank you', icon: 'success', text: res.message || res.pesan })
                        .then(function () { window.location.reload(); });
                } else {
                    Swal.fire({ title: 'Information', icon: 'error', text: res.message || res.pesan });
                    button.prop('disabled', false).find('span').text('Submit');
                }
            }).fail(function (xhr, textStatus, errorThrown) {
                Swal.fire({ title: 'Error', icon: 'error', text: (xhr.responseJSON && xhr.responseJSON.message) || (textStatus + ' : ' + errorThrown) });
                button.prop('disabled', false).find('span').text('Submit');
            });
        });
    });
</script>
@endpush
