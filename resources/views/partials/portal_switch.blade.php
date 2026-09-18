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
<div class="dropdown-inner">
    <ul class="link-list">
        @if ($switchAdmin)
        <li><a href="{{ url('/switch/admin') }}"><em class="icon ni ni-swap"></em><span>Pindah ke Admin</span></a></li>
        @endif
        @foreach ($switchTenants as $t)
        <li><a href="{{ url('/switch/tenant/'.$t['id']) }}"><em class="icon ni ni-swap"></em><span>Pindah ke Tenant: {{ $t['name'] }}</span></a></li>
        @endforeach
    </ul>
</div>
@endif
