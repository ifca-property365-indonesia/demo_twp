@extends('admin.template.layout2.base')
@section('content')

<style type="text/css">
    .toolbar {
        float: left;
        margin-bottom: 1em;
    }

    .password-error {
        color: #e85347;
        font-size: 13px;
        margin-top: 5px;
        display: none;
    }

    .password-success {
        color: #1ee0ac;
        font-size: 13px;
        margin-top: 5px;
        display: none;
    }
</style>

<div class="page-body">
    <div>
        <div class="page-block">
        <div class="page-head">
                <div class="page-head-row">
                    <div class="page-head-content">
                        <h3 class="page-title">{{ __('admin/sysspec.default_password') }}</h3>
                    </div>
                </div>
            </div>

        <div class="card">
            <div class="card-body">
                <div class="container">

                    <div class="row g-4">
                        <div class="col-12 col-sm-12">

                            <h5>{{ __('admin/sysspec.set_default_desc') }}</h5>

                            {{-- Current Password --}}
                            <div class="mb-3">
                                <label class="form-label" for="oldpass">
                                    {{ __('admin/sysspec.current_password') }}
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    name="oldpass"
                                    id="oldpass"
                                    value="{{ $data->password }}"
                                    readonly
                                    disabled
                                >
                            </div>

                            {{-- New Password --}}
                            <div class="mb-3">
                                <label class="form-label" for="newpass">
                                    {{ __('admin/sysspec.new_password') }}
                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    name="newpass"
                                    id="newpass"
                                    placeholder="{{ __('admin/sysspec.enter_new_password') }}"
                                >
                            </div>

                            {{-- Confirm New Password --}}
                            <div class="mb-3">
                                <label class="form-label" for="confirmpass">
                                    {{ __('admin/sysspec.confirm_new_password') }}
                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    name="confirmpass"
                                    id="confirmpass"
                                    placeholder="{{ __('admin/sysspec.confirm_placeholder') }}"
                                >

                                <div id="passwordError" class="password-error">
                                    {{ __('admin/sysspec.password_mismatch') }}
                                </div>

                                <div id="passwordSuccess" class="password-success">
                                    {{ __('admin/sysspec.password_matched') }}
                                </div>
                            </div>

                            <button
                                type="button"
                                class="btn btn-primary mt-2"
                                id="btnsave"
                            >
                                {{ __('common.save') }}
                            </button>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

</div>
<div id="overlaySpinner"
     style="
        display:none;
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100%;
        background:rgba(0,0,0,0.5);
        z-index:9999;
        align-items:center;
        justify-content:center;
     ">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">{{ __('common.loading') }}</span>
    </div>
</div>
<script type = "text/javascript"> 
$(document).ready(function() {
    function checkPassword() {
        var newPassword = $('#newpass').val();
        var confirmPassword = $('#confirmpass').val();
        if (confirmPassword === '') {
            $('#confirmpass').removeClass('is-invalid is-valid');
            $('#passwordError').hide();
            $('#passwordSuccess').hide();
            return false;
        }
        if (newPassword !== confirmPassword) {
            $('#confirmpass').removeClass('is-valid').addClass('is-invalid');
            $('#passwordError').show();
            $('#passwordSuccess').hide();
            return false;
        }
        $('#confirmpass').removeClass('is-invalid').addClass('is-valid');
        $('#passwordError').hide();
        $('#passwordSuccess').show();
        return true;
    }
    $('#newpass, #confirmpass').on('keyup input', function() {
        checkPassword();
    });
    $('#btnsave').click(function(event) {
        event.preventDefault();
        var newPassword = $('#newpass').val();
        var confirmPassword = $('#confirmpass').val();
        if (newPassword === '') {
            Swal.fire({
                title: @json(__('common.information')),
                icon: "warning",
                text: @json(__('admin/sysspec.new_required')),
                confirmButtonText: @json(__('common.ok'))
            });
            $('#newpass').focus();
            return;
        }
        if (confirmPassword === '') {
            Swal.fire({
                title: @json(__('common.information')),
                icon: "warning",
                text: @json(__('admin/sysspec.confirm_required')),
                confirmButtonText: @json(__('common.ok'))
            });
            $('#confirmpass').focus();
            return;
        }
        if (!checkPassword()) {
            Swal.fire({
                title: @json(__('common.information')),
                icon: "error",
                text: @json(__('admin/sysspec.password_mismatch')),
                confirmButtonText: @json(__('common.ok'))
            });
            $('#confirmpass').focus();
            return;
        }
        $('#btnsave').prop('disabled', true);
        $('#overlaySpinner').css('display', 'flex');
        var startTime = Date.now();
        $.ajax({
            url: "{{ url('/admin/systemspec/defaultpasssave') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                password: newPassword,
                confirm_password: confirmPassword
            },
            dataType: "json",
            success: function(response) {
                var elapsed = Date.now() - startTime;
                var remaining = Math.max(0, 3000 - elapsed);
                setTimeout(function() {
                    $('#overlaySpinner').hide();
                    if (response.success) {
                        Swal.fire({
                            title: @json(__('common.information')),
                            icon: "success",
                            text: response.message,
                            confirmButtonText: @json(__('common.ok'))
                        }).then(function() {
                            window.location.reload();
                        });
                    } else {
                        $('#btnsave').prop('disabled', false);
                        Swal.fire({
                            title: @json(__('common.information')),
                            icon: "error",
                            text: response.message || @json(__('admin/sysspec.update_failed')),
                            confirmButtonText: @json(__('common.ok'))
                        });
                    }
                }, remaining);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                var elapsed = Date.now() - startTime;
                var remaining = Math.max(0, 3000 - elapsed);
                setTimeout(function() {
                    $('#overlaySpinner').hide();
                    $('#btnsave').prop('disabled', false);
                    var message = @json(__('admin/sysspec.update_failed'));
                    if (jqXHR.responseJSON) {
                        if (jqXHR.responseJSON.message) {
                            message = jqXHR.responseJSON.message;
                        }
                        if (jqXHR.responseJSON.errors) {
                            var errors = jqXHR.responseJSON.errors;
                            message = '';
                            $.each(errors, function(key, value) {
                                message += value[0] + '<br>';
                            });
                        }
                    }
                    Swal.fire({
                        title: @json(__('common.error')),
                        icon: "error",
                        html: message,
                        confirmButtonText: @json(__('common.ok'))
                    });
                }, remaining);
            }
        });
    });
}); 
</script>

@endsection
