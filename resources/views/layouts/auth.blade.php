{{-- Layout halaman login (tanpa sidebar/header). Section: title, content. Stack: scripts. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ url('/img/logoweb/logoweb.png') }}">
    <title>@yield('title', __('shared/login.page_title')) - {{ __('shared/login.portal_name') }}</title>

    <link rel="stylesheet" href="{{ url('assets/coreui/css/coreui.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/coreui/icons/css/free.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/vendor/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/vendor/select2/select2-bootstrap-5-theme.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/app/css/app.css?ver=1.0.10') }}">
    <style>
        :root { --login-bg: url("{{ asset('public/lainnya/img/Background_new.jpeg') }}"); }
    </style>
</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-brand">
            <img src="{{ url('/img/logoweb/carstensz-logo-white.png') }}" alt="Carstensz">
        </div>
        <div class="card login-card">
            <div class="card-body">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ url('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('assets/coreui/js/coreui.bundle.min.js') }}"></script>
    <script src="{{ url('assets/vendor/select2/select2.full.min.js') }}"></script>
    <script>
        // Dropdown tampil seperti Select2 di portal (tema bootstrap-5, lihat assets/app/js/app.js)
        $(function () {
            $('select.form-select, select.form-control').not('[data-native]').each(function () {
                $(this).select2({ theme: 'bootstrap-5', width: '100%', placeholder: $(this).data('placeholder') || undefined });
            });
        });

        // Tombol lihat/sembunyikan password
        document.querySelectorAll('.toggle-password').forEach(function (el) {
            el.addEventListener('click', function () {
                var input = document.getElementById(el.getAttribute('data-target'));
                var icon = el.querySelector('i');
                if (!input) return;
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                icon.className = show ? 'cil-lock-unlocked' : 'cil-lock-locked';
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
