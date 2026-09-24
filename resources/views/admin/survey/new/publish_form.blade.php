<form id="formPublish">
    @csrf
    <input type="hidden" name="survey_id" value="{{ $survey->id }}">
    
    <div class="mb-3">
        <label class="form-label">{{ __('admin/survey.survey_title') }}</label>
        <input type="text" class="form-control" value="{{ $survey->title }}" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('admin/survey.publish_date') }} <span class="text-danger">*</span></label>
        <div class="form-control-wrap">
            <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
            <input type="text" name="publish_date" id="publish_date" class="form-control date-picker" data-date-format="dd/mm/yyyy" placeholder="{{ __('common.select_date') }}" autocomplete="off" required>
        </div>
        <div id="publish_date_error" class="text-danger fw-bold mt-1" style="display: none; font-size: 12px;">
            <i class="cil-warning"></i> {{ __('admin/survey.publish_before_today') }}
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('admin/survey.expired_date') }} <span class="text-danger">*</span></label>
        <div class="form-control-wrap">
            <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
            <input type="text" name="expired_date" id="expired_date" class="form-control date-picker" data-date-format="dd/mm/yyyy" placeholder="{{ __('common.select_date') }}" autocomplete="off" required>
        </div>
        <div id="expired_date_error" class="text-danger fw-bold mt-1" style="display: none; font-size: 12px;">
            <i class="cil-warning"></i> {{ __('admin/survey.expired_before_publish') }}
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        var today = new Date();
        today.setHours(0,0,0,0);

        // Datepicker dd/mm/yyyy (sama dengan History Invoice); minimal hari ini
        $('#publish_date, #expired_date').datepicker({
            format: 'dd/mm/yyyy', autoclose: true, todayHighlight: true,
            orientation: 'bottom auto', startDate: today
        });
        // expired tidak bisa dipilih sebelum publish
        $('#publish_date').on('change', function () {
            var pub = $(this).datepicker('getDate');
            $('#expired_date').datepicker('setStartDate', pub || today);
        });

        // tanggal dari datepicker (null kalau kosong)
        function pickerDate(sel) {
            var d = $(sel).val() ? $(sel).datepicker('getDate') : null;
            if (d) { d.setHours(0,0,0,0); }
            return d;
        }

        // Validasi Real-time
        function validateDates() {
            var pubVal = $('#publish_date').val();
            var expVal = $('#expired_date').val();
            var isValid = true;

            if (pubVal) {
                var pubDate = pickerDate('#publish_date');

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
                var expDate = pickerDate('#expired_date');
                var comparePub = pickerDate('#publish_date') || today;

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