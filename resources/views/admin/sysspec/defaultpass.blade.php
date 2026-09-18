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

<div class="nk-content-body">
    <div class="components-preview wide-md mx-auto">
        <div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title">Default Password</h4>
            </div>
        </div>

        <div class="card card-preview">
            <div class="card-inner">
                <div class="container">

                    <div class="row g-4">
                        <div class="col-12 col-sm-12">

                            <h5>Set default password for new user</h5>

                            {{-- Old Password --}}
                            <div class="mb-3">
                                <label class="form-label" for="oldpass">
                                    Old Password
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
                                    New Password
                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    name="newpass"
                                    id="newpass"
                                    placeholder="Enter new password"
                                >
                            </div>

                            {{-- Confirm New Password --}}
                            <div class="mb-3">
                                <label class="form-label" for="confirmpass">
                                    Confirm New Password
                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    name="confirmpass"
                                    id="confirmpass"
                                    placeholder="Confirm new password"
                                >

                                <div id="passwordError" class="password-error">
                                    New Password and Confirm New Password do not match.
                                </div>

                                <div id="passwordSuccess" class="password-success">
                                    Password matched.
                                </div>
                            </div>

                            <button
                                type="button"
                                class="btn btn-primary mt-2"
                                id="btnsave"
                            >
                                Save
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
        <span class="visually-hidden">Loading...</span>
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
                title: "Information",
                icon: "warning",
                text: "New Password is required.",
                confirmButtonText: "OK"
            });
            $('#newpass').focus();
            return;
        }
        if (confirmPassword === '') {
            Swal.fire({
                title: "Information",
                icon: "warning",
                text: "Confirm New Password is required.",
                confirmButtonText: "OK"
            });
            $('#confirmpass').focus();
            return;
        }
        if (!checkPassword()) {
            Swal.fire({
                title: "Information",
                icon: "error",
                text: "New Password and Confirm New Password do not match.",
                confirmButtonText: "OK"
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
                            title: "Information",
                            icon: "success",
                            text: response.message,
                            confirmButtonText: "OK"
                        }).then(function() {
                            window.location.reload();
                        });
                    } else {
                        $('#btnsave').prop('disabled', false);
                        Swal.fire({
                            title: "Information",
                            icon: "error",
                            text: response.message || "Failed to update default password.",
                            confirmButtonText: "OK"
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
                    var message = "Failed to update default password.";
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
                        title: "Error",
                        icon: "error",
                        html: message,
                        confirmButtonText: "OK"
                    });
                }, remaining);
            }
        });
    });
}); 
</script>

@endsection
