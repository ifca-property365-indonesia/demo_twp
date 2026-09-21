<div class="nk-sidebar is-capital" data-content="sidebarMenu">
    <div class="nk-sidebar-inner" data-simplebar>
        <ul class="nk-menu nk-menu-md">
            <li class="nk-menu-heading">
                <h6 class="overline-title text-primary-alt">Dashboards</h6>
            </li>
            <li class="nk-menu-item {{ request()->is('admin/dash*') ? 'active' : '' }}">
                <a href="{{ url('/admin/dash') }}" class="nk-menu-link">
                    <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                    <span class="nk-menu-text">Dashboard</span>
                </a>
            </li>

            <li class="nk-menu-heading">
                <h6 class="overline-title text-primary-alt">MENU</h6>
            </li>
            
            <!-- NEWS FEED -->
            <li class="nk-menu-item has-sub {{ request()->is('admin/news*') ? 'active current-page' : '' }}">
                <a href="#" class="nk-menu-link nk-menu-toggle">
                    <span class="nk-menu-icon"><em class="icon ni ni-notice"></em></span><span class="nk-menu-text">News Feed</span>
                </a>
                <ul class="nk-menu-sub" style="{{ request()->is('admin/news*') ? 'display: block;' : '' }}">
                    <li class="nk-menu-item {{ request()->is('admin/news/form/*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/news/form/A') }}" class="nk-menu-link"><span class="nk-menu-text">Create News</span></a>
                    </li>
                    <li class="nk-menu-item {{ (request()->fullUrl() == url('/admin/news') || request()->fullUrl() == url('/admin/news/')) ? 'active' : '' }}">
                        <a href="{{ url('/admin/news') }}" class="nk-menu-link"><span class="nk-menu-text">List News</span></a>
                    </li>
                </ul>                   
            </li>

            <!-- ONLINE SURVEY BARU -->
            <li class="nk-menu-item has-sub {{ request()->is('admin/usersurvey*') ? 'active current-page' : '' }}">
                <a href="#" class="nk-menu-link nk-menu-toggle">
                    <span class="nk-menu-icon"><em class="icon ni ni-list-check"></em></span><span class="nk-menu-text">Online Survey</span>
                </a>
                <ul class="nk-menu-sub" style="{{ request()->is('admin/usersurvey*') ? 'display: block;' : '' }}">
                    <li class="nk-menu-item {{ (request()->fullUrl() == url('/admin/usersurvey') || request()->fullUrl() == url('/admin/usersurvey/')) ? 'active' : '' }}">
                        <a href="{{ url('/admin/usersurvey/') }}" class="nk-menu-link"><span class="nk-menu-text">Survey Questions</span></a>
                    </li>
                    <li class="nk-menu-item {{ request()->is('admin/usersurvey/results*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/usersurvey/results') }}" class="nk-menu-link"><span class="nk-menu-text">Survey Results</span></a>
                    </li>
                </ul>   
            </li>
            
            @if(Session::get('Tsuname') == 'Admin Management')
            <li class="nk-menu-item {{ request()->is('admin/management*') ? 'active' : '' }}">
                <a href="{{ url('/admin/management') }}" class="nk-menu-link">
                    <span class="nk-menu-icon"><em class="icon ni ni-bar-chart"></em></span>
                    <span class="nk-menu-text">Graph Management</span>
                </a>
            </li>
            @endif
            
            <!-- HISTORY -->
            <li class="nk-menu-item has-sub {{ request()->is('admin/history*') ? 'active current-page' : '' }}">
                <a href="#" class="nk-menu-link nk-menu-toggle">
                    <span class="nk-menu-icon"><em class="icon ni ni-histroy"></em></span><span class="nk-menu-text">History</span>
                </a>
                <ul class="nk-menu-sub" style="{{ request()->is('admin/history*') ? 'display: block;' : '' }}">
                    <li class="nk-menu-item {{ request()->is('admin/history/ticket*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/history/ticket') }}" class="nk-menu-link"><span class="nk-menu-text">Ticket</span></a>
                    </li>
                    <li class="nk-menu-item {{ request()->is('admin/history/users*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/history/users') }}" class="nk-menu-link"><span class="nk-menu-text">Log user</span></a>
                    </li>
                </ul>   
            </li>
            
            <!-- PASSWORD -->
            @php $passwordOpen = request()->is('admin/account/reset*') || request()->is('admin/systemspec/defaultpass*'); @endphp
            <li class="nk-menu-item has-sub {{ $passwordOpen ? 'active current-page' : '' }}">
                <a href="#" class="nk-menu-link nk-menu-toggle">
                    <span class="nk-menu-icon"><em class="icon ni ni-lock-alt"></em></span><span class="nk-menu-text">Password</span>
                </a>
                <ul class="nk-menu-sub" style="{{ $passwordOpen ? 'display: block;' : '' }}">
                    <li class="nk-menu-item {{ request()->is('admin/account/reset*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/account/reset') }}" class="nk-menu-link"><span class="nk-menu-text">Password Reset</span></a>
                    </li>
                    <li class="nk-menu-item {{ request()->is('admin/systemspec/defaultpass*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/systemspec/defaultpass') }}" class="nk-menu-link"><span class="nk-menu-text">Default Password</span></a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>

<!-- SCRIPT UNTUK MEMATIKAN BUG ACTIVE JS TEMPLATE -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    setTimeout(function() {
        const currentUrl = window.location.href.split('?')[0].replace(/\/$/, "");

        document.querySelectorAll('.nk-sidebar .nk-menu-item').forEach(function(item) {
            const link = item.querySelector('a.nk-menu-link');
            if (link) {
                const href = link.getAttribute('href').replace(/\/$/, "");
                
                // Jika URL elemen tidak cocok presisi dengan URL browser, cabut class active
                if (href && href !== '#' && href !== currentUrl) {
                    item.classList.remove('active', 'current-page');
                } else if (href === currentUrl) {
                    item.classList.add('active');
                }
            }
        });
    }, 100); // Delay 100ms untuk memastikan JS bawaan template selesai mengeksekusi scriptnya terlebih dahulu
});
</script>