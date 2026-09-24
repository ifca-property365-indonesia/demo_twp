{{--
    Menu "pindah portal" di dropdown user (header admin & tenant).
    Dipakai: @include('partials.portal_switch', ['current' => 'admin' | 'tenant'])
    Sumber data: session 'portals' yang diisi PortalLoginController saat login
    (hanya portal yang password-nya cocok). Tidak tampil kalau tidak ada portal lain.
--}}
@php
    $portals = session('portals', []);
    $switchAdmin = $current !== 'admin' && !empty($portals['admin']);
    $switchTenants = [];
    foreach ($portals['tenants'] ?? [] as $t) {
        if ($current === 'tenant' && session('Tuser_id') == $t['id']) continue;
        $switchTenants[] = $t;
    }
@endphp
@if ($switchAdmin || count($switchTenants) > 0)
    <div class="dropdown-header bg-body-tertiary fw-semibold text-body-secondary small">Switch portal</div>
    @if ($switchAdmin)
        <a class="dropdown-item" href="{{ url('/switch/admin') }}"><i class="cil-swap-horizontal"></i> Switch to Admin</a>
    @endif
    @foreach ($switchTenants as $t)
        <a class="dropdown-item" href="{{ url('/switch/tenant/'.$t['id']) }}"><i class="cil-swap-horizontal"></i> Switch to Tenant: {{ $t['name'] }}</a>
    @endforeach
    <div class="dropdown-divider"></div>
@endif
