@php
    // Menu aktif mengikuti URL saat ini.
    $newsOpen     = request()->is('admin/news*');
    $surveyOpen   = request()->is('admin/usersurvey*');
    $historyOpen  = request()->is('admin/history*');
    $passwordOpen = request()->is('admin/account/reset*') || request()->is('admin/systemspec/defaultpass*');
    $isExact = function ($path) {
        return rtrim(request()->path(), '/') === trim($path, '/');
    };
@endphp
<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header border-bottom">
        <div class="sidebar-brand">
            <a href="{{ url('/admin/dash') }}" class="d-flex align-items-center gap-2 text-decoration-none text-white">
                <img src="{{ url('/images/logoweb/logoweb.png') }}" alt="IFCA">
                <span class="fw-bold">Web Admin</span>
            </a>
        </div>
        <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="Close"
                onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation">
        <li class="nav-title">Dashboards</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/dash*') ? 'active' : '' }}" href="{{ url('/admin/dash') }}">
                <i class="nav-icon cil-speedometer"></i> Dashboard
            </a>
        </li>

        <li class="nav-title">Menu</li>

        {{-- News feed --}}
        <li class="nav-group {{ $newsOpen ? 'show' : '' }}">
            <a class="nav-link nav-group-toggle" href="#"><i class="nav-icon cil-newspaper"></i> News Feed</a>
            <ul class="nav-group-items compact">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/news/form/*') ? 'active' : '' }}" href="{{ url('/admin/news/form/A') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Create News
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $isExact('admin/news') ? 'active' : '' }}" href="{{ url('/admin/news') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span> List News
                    </a>
                </li>
            </ul>
        </li>

        {{-- Online survey --}}
        <li class="nav-group {{ $surveyOpen ? 'show' : '' }}">
            <a class="nav-link nav-group-toggle" href="#"><i class="nav-icon cil-task"></i> Online Survey</a>
            <ul class="nav-group-items compact">
                <li class="nav-item">
                    <a class="nav-link {{ $isExact('admin/usersurvey') ? 'active' : '' }}" href="{{ url('/admin/usersurvey/') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Survey Questions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/usersurvey/results*') ? 'active' : '' }}" href="{{ url('/admin/usersurvey/results') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Survey Results
                    </a>
                </li>
            </ul>
        </li>

        @if(Session::get('Tsuname') == 'Admin Management')
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/management*') ? 'active' : '' }}" href="{{ url('/admin/management') }}">
                <i class="nav-icon cil-bar-chart"></i> Graph Management
            </a>
        </li>
        @endif

        {{-- History --}}
        <li class="nav-group {{ $historyOpen ? 'show' : '' }}">
            <a class="nav-link nav-group-toggle" href="#"><i class="nav-icon cil-history"></i> History</a>
            <ul class="nav-group-items compact">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/history/ticket*') ? 'active' : '' }}" href="{{ url('/admin/history/ticket') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Ticket
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/history/users*') ? 'active' : '' }}" href="{{ url('/admin/history/users') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Log User
                    </a>
                </li>
            </ul>
        </li>

        {{-- Password --}}
        <li class="nav-group {{ $passwordOpen ? 'show' : '' }}">
            <a class="nav-link nav-group-toggle" href="#"><i class="nav-icon cil-lock-locked"></i> Password</a>
            <ul class="nav-group-items compact">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/account/reset*') ? 'active' : '' }}" href="{{ url('/admin/account/reset') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Password Reset
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/systemspec/defaultpass*') ? 'active' : '' }}" href="{{ url('/admin/systemspec/defaultpass') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Default Password
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</div>
