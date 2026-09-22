@extends('tenant.template.base')

@section('title', 'Online Survey')

@section('content')
    <div class="page-body">
        <div class="page-head">
            <div class="page-head-row">
                <div class="page-head-content">
                    <h3 class="page-title">Take Survey</h3>
                </div>
            </div>
        </div>
        <div class="page-block">
            <div class="card">
                <div class="card-body survey-forms">
                    @if (!empty($dP))
                        {!! $dP !!}
                    @else
                        <div class="text-center py-5 text-body-secondary">
                            <i class="cil-task fs-1 d-block mb-2"></i>
                            No survey available.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .survey-forms form[id^="frm"] { border: 1px solid var(--cui-border-color); border-radius: .5rem; padding: 1.25rem; margin-bottom: 1.5rem; }
    .survey-forms form[id^="frm"]:last-child { margin-bottom: 0; }
</style>
@endpush

@push('scripts')
<script type="text/javascript">
    $(function () {
        $('input[type="radio"]').on('click', function () {
            var $remarks = $(this).closest('.form-check').find('textarea[name="remarks"]');
            if ($(this).data('ada') == true) {
                $remarks.val('').prop('disabled', false).trigger('focus');
            } else {
                $remarks.val('').prop('disabled', true);
            }
        });

        $(document).on('click', '[id^="btnSave"]', function (event) {
            event.preventDefault();
            var button = $(this);
            var publishId = button.data('p');
            var $form = $('#frm' + publishId);

            if (!$form.valid()) {
                return;
            }

            button.prop('disabled', true);

            $.ajax({
                url: "{{ url('/tenant/online_survey/save') }}",
                type: 'POST',
                data: $form.serializeArray(),
                dataType: 'json'
            }).done(function (res) {
                if (res.status == 'OK') {
                    Swal.fire({ title: 'Information', icon: 'success', text: res.pesan })
                        .then(function () { window.location.href = "{{ url('/tenant/online_survey') }}"; });
                } else {
                    Swal.fire({ title: 'Information', icon: 'error', text: res.pesan });
                    button.prop('disabled', false);
                }
            }).fail(function (xhr, textStatus, errorThrown) {
                Swal.fire({ title: 'Error', icon: 'error', text: textStatus + ' : ' + errorThrown });
                button.prop('disabled', false);
            });
        });
    });
</script>
@endpush
