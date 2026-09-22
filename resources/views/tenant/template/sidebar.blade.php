@php
    // Menu aktif mengikuti URL saat ini.
    $isOperational = session('Tflag') == 'O';
    $historyOpen = request()->is('tenant/history*');
@endphp
<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header border-bottom">
        <div class="sidebar-brand sidebar-brand-tenant">
            <a href="https://carstensz.co.id/mall" target="_blank" rel="noopener noreferrer">
                <img src="{{ url('/img/logoweb/carstensz-logo-new2.png') }}" alt="Carstensz">
            </a>
        </div>
        <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="Close"
                onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation">
        <li class="nav-title">Dashboard</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('tenant/dash*') ? 'active' : '' }}" href="{{ url('/tenant/dash') }}">
                <i class="nav-icon cil-speedometer"></i> Dashboard
            </a>
        </li>

        <li class="nav-title">Menu</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('tenant/ticket*') ? 'active' : '' }}" href="{{ url('/tenant/ticket') }}">
                <i class="nav-icon cil-tags"></i> Ticket
            </a>
        </li>
        @unless($isOperational)
        <li class="nav-item">
            <a class="nav-link {{ request()->is('tenant/proforma*') ? 'active' : '' }}" href="{{ url('/tenant/proforma') }}">
                <i class="nav-icon cil-description"></i> Proforma Invoice
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('tenant/invoice*') ? 'active' : '' }}" href="{{ url('/tenant/invoice') }}">
                <i class="nav-icon cil-wallet"></i> Invoice Outstanding
            </a>
        </li>
        @endunless
        <li class="nav-group {{ $historyOpen ? 'show' : '' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <i class="nav-icon cil-history"></i> History
            </a>
            <ul class="nav-group-items compact">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('tenant/history/ticket*') ? 'active' : '' }}" href="{{ url('/tenant/history/ticket') }}" id="ht">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Ticket
                    </a>
                </li>
                @unless($isOperational)
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('tenant/history/invoice*') ? 'active' : '' }}" href="{{ url('/tenant/history/invoice') }}" id="hb">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Invoice
                    </a>
                </li>
                @endunless
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('tenant/news*') ? 'active' : '' }}" href="{{ url('/tenant/news') }}">
                <i class="nav-icon cil-newspaper"></i> News
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('tenant/usersurvey*') || request()->is('tenant/online_survey*') ? 'active' : '' }}" href="{{ url('/tenant/usersurvey/index') }}">
                <i class="nav-icon cil-task"></i> Online Survey
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('tenant/permit*') ? 'active' : '' }}" href="{{ url('/tenant/permit/history') }}">
                <i class="nav-icon cil-clipboard"></i> Letter Permit
            </a>
        </li>
    </ul>
</div>
