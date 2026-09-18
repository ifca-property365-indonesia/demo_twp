<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Cartenz | Log in</title>

        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <link rel="shortcut icon" href="{{ url('/img/logoweb/logoweb.png') }}">
        <!-- CSS -->
        <link href="{{ asset('public/lainnya/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('public/lainnya/plugins/font-awesome-4.4.0/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('public/AssetsLogin/css/AdminLTE.min.css') }}" rel="stylesheet">
        <link href="{{ asset('public/AssetsLogin/css/square/blue.css') }}" rel="stylesheet">

        <style>
            html, body {
                height: 100%;
                margin: 0;
            }

            .login-page {
                position: relative;
                overflow: hidden;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            /* Background blur */
            .login-page::before {
                content: "";
                position: absolute;
                inset: -20px;
                background: url("{{ asset('public/lainnya/img/Background_new.jpeg') }}") no-repeat center center;
                background-size: cover;
                filter: blur(8px);
                transform: scale(1.1);
                z-index: 0;
            }

            .login-page > * {
                position: relative;
                z-index: 1;
            }

            /* Container */
            .login-box {
                width: 100%;
                max-width: 420px;
            }

            /* Card */
            .login-box-body {
                padding: 30px 25px;
                border-radius: 12px;
            }

            /* Title */
            .login-title {
                font-size: 30px;
                color: #FFFFFF;
                text-align: center;
                margin-bottom: 5px;
				font-family: "Century Gothic", CenturyGothic, AppleGothic, sans-serif;
            }

            .login-subtitle {
                font-size: 20px;
                color: #FFFFFF;
                text-align: center;
                margin-bottom: 20px;
				font-family: "Century Gothic", CenturyGothic, AppleGothic, sans-serif;
            }

            /* Form spacing */
            .form-group {
                margin-bottom: 18px;
            }

            .login-logo2 {
                font-size: 35px;
                text-align: center;
                font-weight: 300;
            }

            /* Mobile */
            @media (max-width: 480px) {
                .login-title {
                    font-size: 24px;
					font-family: "Century Gothic", CenturyGothic, AppleGothic, sans-serif;
                }

                .login-subtitle {
                    font-size: 18px;
					font-family: "Century Gothic", CenturyGothic, AppleGothic, sans-serif;
                }

                .login-box-body {
                    padding: 20px 15px;
                }
            }
            select.form-control {
                height: 45px;
                font-size: 16px;
            }
        </style>
    </head>

<body class="login-page">
    <div class="login-box">

        <div class="login-box-body">
            <p class="login-title">
                <img
                    src="{{ url('/img/logoweb/carstensz-logo-white.png') }}"
                    alt="Cartenz"
                    class="login-logo2">
            </p>
            <p class="login-subtitle">Tenant Log In</p>

            <form action="{{ url('/tenant/login') }}" method="POST" id="formlogin" class="needs-validation" novalidate="">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="default-01">Business Name</label>
                    </div>
                    <select id="bsn" name="bsn" class="form-control form-control-lg" required="true">
                        <?php echo $combo;?>
                    </select>
                </div><!-- .foem-group -->
                <div class="form-group">
                    <div class="form-label-group">
                        <label>Password</label>
                    </div>

                    <div style="position:relative;">
                        <input
                            type="password"
                            class="form-control form-control-lg"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            style="padding-right:45px;"
                        >

                        <span id="togglePassword"
                            style="position:absolute;right:15px;top:50%;transform:translateY(-50%);cursor:pointer;color:#000;">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-primary btn-block">Go</button>
                </div>
            </form><!-- form -->

        </div>
    </div>
    <script>
        document.getElementById('togglePassword').onclick = function () {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        };
    </script>

    <!-- JS -->
    <script src="{{ asset('public/lainnya/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('public/AssetsLogin/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/AssetsLogin/js/icheck.min.js') }}"></script>

    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
        });
    </script>

    </body>
</html>