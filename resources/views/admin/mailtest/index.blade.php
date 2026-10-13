@extends('admin.template.layout2.base')
@section('title', __('admin/mailtest.title'))

@section('content')
<div class="page-body">
    <div class="page-head">
        <div class="page-head-row">
            <div class="page-head-content">
                <h3 class="page-title">{{ __('admin/mailtest.title') }}</h3>
                <div class="page-desc"><p>{{ __('admin/mailtest.desc') }}</p></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="frmMailTest" novalidate autocomplete="off">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label" for="email">{{ __('admin/mailtest.email') }} <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="{{ __('admin/mailtest.email_placeholder') }}" required>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary" id="btnSend">
                            <i class="cil-send"></i><span>{{ __('admin/mailtest.send') }}</span>
                        </button>
                    </div>
                </div>
            </form>

            {{-- pengaturan SMTP yang dipakai (tanpa username / password) --}}
            <div class="form-note mt-3">
                {{ __('admin/mailtest.smtp_info', ['host' => $smtp['host'] ?: '-', 'port' => $smtp['port'] ?: '-', 'from' => $smtp['from'] ?: '-']) }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
$(function () {
    $('#frmMailTest').on('submit', function (e) {
        e.preventDefault();
        var email = $.trim($('#email').val());
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            Swal.fire({ icon: 'warning', title: @json(__('common.information')), text: @json(__('admin/mailtest.invalid_email')), confirmButtonText: @json(__('common.ok')) });
            $('#email').focus();
            return;
        }

        $('#btnSend').prop('disabled', true);
        $('#overlaySpinner').css('display', 'flex');

        $.ajax({
            url: "{{ url('/admin/mail-test') }}",
            type: 'POST',
            data: { email: email },
            dataType: 'json'
        }).done(function (res) {
            Swal.fire({ icon: 'success', title: @json(__('common.information')), text: res.pesan, confirmButtonText: @json(__('common.ok')) });
        }).fail(function (xhr) {
            var res = xhr.responseJSON || {};
            Swal.fire({ icon: 'error', title: @json(__('admin/mailtest.failed')), text: res.pesan || xhr.statusText, confirmButtonText: @json(__('common.ok')) });
        }).always(function () {
            $('#overlaySpinner').hide();
            $('#btnSend').prop('disabled', false);
        });
    });
});
</script>
@endpush
