@extends('layouts.auth')

@section('title', __('shared/login.page_title'))

@section('content')
    <h1 class="login-title">{{ __('shared/login.log_in') }}</h1>

    @if (session('alert'))
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="cil-warning me-2"></i><div>{{ session('alert') }}</div>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="cil-warning me-2"></i><div>{{ $errors->first() }}</div>
        </div>
    @endif

    <form action="{{ url('/login') }}" method="POST" id="formlogin" novalidate autocomplete="on">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="email">{{ __('common.email') }}</label>
            <div class="form-control-wrap">
                <div class="form-icon form-icon-left"><i class="cil-envelope-closed"></i></div>
                <input type="email" class="form-control form-control-lg" name="email" id="email"
                       placeholder="{{ __('shared/login.ph_email') }}" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
            {{-- email tidak terdaftar / akun expired (diisi via /login/businesses); password dikunci --}}
            <div class="alert alert-warning d-flex align-items-center py-2 px-3 mt-2 mb-0 small" id="email-status" role="alert" style="display:none !important">
                <i class="cil-warning me-2"></i><div id="email-status-text"></div>
            </div>
        </div>

        {{-- muncul otomatis kalau email adalah tenant (diisi via /login/businesses) --}}
        <div class="mb-3" id="bsn-group" style="display:none">
            <label class="form-label" for="bsn">{{ __('shared/login.business_name') }}</label>
            <div class="form-control-wrap">
                <div class="form-icon form-icon-left"><i class="cil-building"></i></div>
                <select class="form-select" name="bsn" id="bsn" data-old="{{ old('bsn') }}"></select>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label" for="password">{{ __('common.password') }}</label>
            <div class="password-wrap">
                <input type="password" class="form-control form-control-lg" name="password" id="password"
                       placeholder="{{ __('shared/login.ph_password') }}" required autocomplete="current-password">
                <span class="toggle-password" data-target="password" title="{{ __('shared/login.toggle_password') }}"><i class="cil-lock-locked"></i></span>
            </div>
        </div>

        <button type="submit" class="btn btn-lg btn-primary w-100">{{ __('shared/login.log_in') }}</button>
    </form>
@endsection

@push('scripts')
<script>
    // Setelah email diisi: cek apakah email tenant -> tampilkan dropdown Business Name.
    // Password baru bisa diisi setelah pengecekan email selesai.
    (function () {
        var emailEl = document.getElementById('email');
        var group = document.getElementById('bsn-group');
        var select = document.getElementById('bsn');
        var passwordEl = document.getElementById('password');
        var passwordPlaceholder = passwordEl.placeholder;
        var statusEl = document.getElementById('email-status');
        var statusText = document.getElementById('email-status-text');
        var submitBtn = document.querySelector('#formlogin button[type=submit]');
        var lastEmail = null;
        var timer = null;

        // pesan di bawah email (null = sembunyikan)
        function showStatus(message) {
            statusText.textContent = message || '';
            statusEl.style.setProperty('display', message ? 'flex' : 'none', 'important');
        }
        function lockPassword(checking, placeholder) {
            passwordEl.disabled = true;
            passwordEl.value = '';
            passwordEl.placeholder = placeholder || (checking ? @json(__('shared/login.checking_email')) : @json(__('shared/login.enter_email_first')));
        }
        function unlockPassword() {
            passwordEl.disabled = false;
            passwordEl.placeholder = passwordPlaceholder;
            submitBtn.disabled = false;
        }
        lockPassword(false);

        // Respons server: admin (true/false), daftar tenants (id, name), status & message.
        // Email admin selalu masuk sebagai admin (pindah ke tenant lewat menu di header),
        // jadi dropdown business hanya muncul untuk email tenant biasa.
        function render(data) {
            var tenants = data.admin ? [] : (data.tenants || []);
            var list = tenants.slice();

            select.innerHTML = '';
            // email tidak terdaftar / akun expired / tidak aktif: tampilkan info, password tetap terkunci
            if (data.status && data.status !== 'ok') {
                group.style.display = 'none';
                select.required = false;
                showStatus(data.message);
                lockPassword(false, data.message);
                submitBtn.disabled = true;
                return;
            }
            showStatus(null);
            unlockPassword();
            if (!tenants.length) {
                group.style.display = 'none';
                select.required = false;
                return;
            }
            var old = select.getAttribute('data-old');
            if (list.length > 1) {
                var ph = document.createElement('option');
                ph.value = '';
                ph.textContent = @json(__('shared/login.choose'));
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
            showStatus(null);
            submitBtn.disabled = false;
            lastEmail = null;
            clearTimeout(timer);
            timer = setTimeout(load, 500);
        });
        if (emailEl.value) load();
    })();
</script>
@endpush
