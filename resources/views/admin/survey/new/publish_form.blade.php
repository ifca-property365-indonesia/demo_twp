<form id="formPublish">
    @csrf
    <input type="hidden" name="survey_id" value="{{ $survey->id }}">
    
    <div class="mb-3">
        <label class="form-label">{{ __('admin/survey.survey_title') }}</label>
        <input type="text" class="form-control" value="{{ $survey->title }}" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('admin/survey.publish_date') }} <span class="text-danger">*</span></label>
        <input type="date" name="publish_date" id="publish_date" class="form-control" required>
        <div id="publish_date_error" class="text-danger fw-bold mt-1" style="display: none; font-size: 12px;">
            <i class="cil-warning"></i> {{ __('admin/survey.publish_before_today') }}
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('admin/survey.expired_date') }} <span class="text-danger">*</span></label>
        <input type="date" name="expired_date" id="expired_date" class="form-control" required>
        <div id="expired_date_error" class="text-danger fw-bold mt-1" style="display: none; font-size: 12px;">
            <i class="cil-warning"></i> {{ __('admin/survey.expired_before_publish') }}
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        var today = new Date();
        today.setHours(0,0,0,0);
        var todayStr = today.toISOString().split('T')[0];

        $('#publish_date').attr('min', todayStr);
        $('#expired_date').attr('min', todayStr);

        // Validasi Real-time
        function validateDates() {
            var pubVal = $('#publish_date').val();
            var expVal = $('#expired_date').val();
            var isValid = true;

            if (pubVal) {
                var pubDate = new Date(pubVal);
                pubDate.setHours(0,0,0,0);

                if (pubDate < today) {
                    $('#publish_date_error').slideDown();
                    $('#publish_date').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#publish_date_error').slideUp();
                    $('#publish_date').removeClass('is-invalid');
                }
            }

            if (expVal) {
                var expDate = new Date(expVal);
                expDate.setHours(0,0,0,0);
                var comparePub = pubVal ? new Date(pubVal) : today;
                comparePub.setHours(0,0,0,0);

                if (expDate < comparePub) {
                    $('#expired_date_error').text(@json(__('admin/survey.expired_before_publish'))).slideDown();
                    $('#expired_date').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#expired_date_error').slideUp();
                    $('#expired_date').removeClass('is-invalid');
                }
            }

            $('#savefrm_publish').prop('disabled', !isValid);
        }

        $(document).on('change input', '#publish_date, #expired_date', function() {
            validateDates();
        });

        // Submit Action
        $(document).off('click', '#savefrm_publish').on('click', '#savefrm_publish', function(e) {
            e.preventDefault();

            var pubVal = $('#publish_date').val();
            var expVal = $('#expired_date').val();

            if (!pubVal || !expVal) {
                Swal.fire(@json(__('common.information')), @json(__('admin/survey.select_both_dates')), "warning");
                return;
            }

            $.ajax({
                url: "{{ url('/admin/usersurvey/publish-submit') }}",
                type: "POST",
                data: $('#formPublish').serialize(),
                success: function(res) {
                    if(res.status == 'OK'){
                        $('#modalxl').modal('hide');
                        Swal.fire(@json(__('common.information')), res.message || @json(__('admin/survey.survey_published')), "success");
                        
                        if (typeof tbldraft !== 'undefined') tbldraft.ajax.reload(null, false);
                        if (typeof tblpublished !== 'undefined') tblpublished.ajax.reload(null, false);
                    } else {
                        Swal.fire(@json(__('common.information')), res.message || @json(__('admin/survey.publish_survey_failed')), "error");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire(@json(__('admin/survey.failed_status')).replace(':status', jqXHR.status), @json(__('common.error_occurred')).replace(':message', errorThrown), "error");
                }
            });
        });
    });
</script>