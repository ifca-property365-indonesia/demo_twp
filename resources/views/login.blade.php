@extends('layouts.auth')

@section('title', 'Log in')

@section('content')
    <h1 class="login-title">Log In</h1>

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
            <label class="form-label" for="email">Email</label>
            <div class="form-control-wrap">
                <div class="form-icon form-icon-left"><i class="cil-envelope-closed"></i></div>
                <input type="email" class="form-control form-control-lg" name="email" id="email"
                       placeholder="Enter your email address" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
        </div>

        {{-- muncul otomatis kalau email adalah tenant (diisi via /login/businesses) --}}
        <div class="mb-3" id="bsn-group" style="display:none">
            <label class="form-label" for="bsn">Business Name</label>
            <div class="form-control-wrap">
                <div class="form-icon form-icon-left"><i class="cil-building"></i></div>
                <select class="form-select" name="bsn" id="bsn" data-old="{{ old('bsn') }}"></select>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label" for="password">Password</label>
            <div class="password-wrap">
                <input type="password" class="form-control form-control-lg" name="password" id="password"
                       placeholder="Enter your password" required autocomplete="current-password">
                <span class="toggle-password" data-target="password" title="Show / hide password"><i class="cil-lock-locked"></i></span>
            </div>
        </div>

        <button type="submit" class="btn btn-lg btn-primary w-100">Log In</button>
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
</script>
@endpush
