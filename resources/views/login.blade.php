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

            .login-box {
                width: 100%;
                max-width: 420px;
            }

            .login-box-body {
                padding: 30px 25px;
                border-radius: 12px;
            }

            .login-title {
                font-size: 32px;
                color: #bda870;
                text-align: center;
                margin-bottom: 5px;
                font-family: "Century Gothic", CenturyGothic, AppleGothic, sans-serif;
            }

            .login-subtitle {
                font-size: 22px;
                color: #FFFFFF;
                text-align: center;
                margin-bottom: 20px;
                font-family: "Century Gothic", CenturyGothic, AppleGothic, sans-serif;
            }

            .login-logo2 {
                font-size: 35px;
                text-align: center;
                font-weight: 300;
            }

            .password-wrap {
                position: relative;
            }

            .password-wrap .toggle-password {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: #000;
            }

            @media (max-width: 480px) {
                .login-title { font-size: 24px; }
                .login-subtitle { font-size: 18px; }
                .login-box-body { padding: 20px 15px; }
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
            <p class="login-subtitle">Log In</p>

            @if (session('alert'))
                <div class="alert alert-danger" role="alert">{{ session('alert') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
            @endif

            <form action="{{ url('/login') }}" method="POST" id="formlogin" class="needs-validation" novalidate="">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email">Email</label>
                    </div>
                    <input type="text" class="form-control form-control-lg" name="email" id="email" placeholder="Enter your email address" value="{{ old('email') }}" required="true" autofocus>
                </div>
                {{-- muncul otomatis kalau email adalah tenant (diisi via /login/businesses) --}}
                <div class="form-group" id="bsn-group" style="display:none">
                    <div class="form-label-group">
                        <label class="form-label" for="bsn">Business Name</label>
                    </div>
                    <select class="form-control form-control-lg" name="bsn" id="bsn" data-old="{{ old('bsn') }}"></select>
                </div>
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="password">Password</label>
                    </div>
                    <div class="password-wrap">
                        <input type="password" class="form-control form-control-lg" name="password" id="password" placeholder="Enter your password" required="true">
                        <span id="togglePassword" class="toggle-password"><i class="fa fa-eye"></i></span>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-primary btn-block">Go</button>
                </div>
            </form><!-- form -->

        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('public/lainnya/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('public/AssetsLogin/js/bootstrap.min.js') }}"></script>

    <script>
        // Setelah email diisi: cek apakah email tenant -> tampilkan dropdown Business Name.
        // Password baru bisa diisi setelah pengecekan email selesai.
        (function () {
            var emailEl = document.getElementById('email');
            var group = document.getElementById('bsn-group');
            var select = document.getElementById('bsn');
            var passwordEl = document.getElementById('password');
            var passwordPlaceholder = passwordEl.placeholder;
            var lastEmail = null;
            var timer = null;

            function lockPassword(checking) {
                passwordEl.disabled = true;
                passwordEl.value = '';
                passwordEl.placeholder = checking ? 'Memeriksa email...' : 'Isi email terlebih dahulu';
            }
            function unlockPassword() {
                passwordEl.disabled = false;
                passwordEl.placeholder = passwordPlaceholder;
            }
            lockPassword(false);

            // Respons server: admin (true/false) dan daftar tenants (id, name).
            // Email admin selalu masuk sebagai admin (pindah ke tenant lewat menu di header),
            // jadi dropdown business hanya muncul untuk email tenant biasa.
            function render(data) {
                var tenants = data.admin ? [] : (data.tenants || []);
                var list = tenants.slice();

                unlockPassword();
                select.innerHTML = '';
                if (!tenants.length) {
                    group.style.display = 'none';
                    select.required = false;
                    return;
                }
                var old = select.getAttribute('data-old');
                if (list.length > 1) {
                    var ph = document.createElement('option');
                    ph.value = '';
                    ph.textContent = '-- Choose --';
                    select.appendChild(ph);
                }
                list.forEach(function (b) {
                    var opt = document.createElement('option');
                    opt.value = b.id;
                    opt.textContent = b.name;
                    if (String(b.id) === String(old)) opt.selected = true;
                    select.appendChild(opt);
                });
                select.required = list.length > 1;
                group.style.display = '';
            }

            var EMPTY = { admin: false, tenants: [] };
            function load() {
                var email = emailEl.value.trim();
                if (email === lastEmail) return;
                lastEmail = email;
                if (email.indexOf('@') < 0) {
                    // belum berupa email: sembunyikan dropdown, password tetap terkunci
                    select.innerHTML = '';
                    group.style.display = 'none';
                    select.required = false;
                    lockPassword(false);
                    return;
                }
                lockPassword(true);
                $.getJSON("{{ url('/login/businesses') }}", { email: email })
                    .done(function (data) { if (emailEl.value.trim() === email) render(data); })
                    .fail(function () { if (emailEl.value.trim() === email) render(EMPTY); });
            }

            emailEl.addEventListener('blur', load);
            emailEl.addEventListener('input', function () {
                lockPassword(false);   // email berubah -> harus dicek ulang
                lastEmail = null;
                clearTimeout(timer);
                timer = setTimeout(load, 500);
            });
            if (emailEl.value) load();
        })();

        document.getElementById('togglePassword').onclick = function () {
            var password = document.getElementById('password');
            var icon = this.querySelector('i');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        };
    </script>

    </body>
</html>
