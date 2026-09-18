<div class="nk-sidebar nk-sidebar-fixed is-dark " data-content="sidebarMenu" style="width: 250px;">
    <div class="nk-sidebar-element nk-sidebar-head" style="width: 250px;height: 165px;">
        <div class="nk-sidebar-brand">
            <a href="https://carstensz.co.id/mall" class="logo-link nk-sidebar-logo" target="_blank" rel="noopener noreferrer">
                <img class="logo-light logo-img"
                    src="{{ url('/img/logoweb/carstensz-logo-new2.png') }}"
                    alt="logo"
                    style="max-height: 136px; margin-left: 10%;">
            </a>
        </div>
        <div class="nk-menu-trigger mr-n2">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
        </div>
    </div><!-- .nk-sidebar-element -->
    <div class="nk-sidebar-element">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Dashboard</h6>
                    </li><!-- .nk-menu-heading -->
                    <li class="nk-menu-item">
                        <a href="{{ url('/tenant/dash') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                            <span class="nk-menu-text"> Dashboard</span>
                        </a>
                    </li><!-- .nk-menu-item -->
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Menu</h6>
                    </li><!-- .nk-menu-heading -->
                    <li class="nk-menu-item">
                        <a href="{{ url('/tenant/ticket') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-ticket"></em></span>
                            <span class="nk-menu-text"> Ticket</span>
                        </a>
                    </li><!-- .nk-menu-item -->
                    @unless(session('Tflag') == 'O')
                    <li class="nk-menu-item">
                        <a href="{{ url('/tenant/proforma') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-report-profit"></em></span>
                            <span class="nk-menu-text"> Proforma Invoice</span>
                        </a>
                    </li><!-- .nk-menu-item -->
                    <li class="nk-menu-item">
                        <a href="{{ url('/tenant/invoice') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-report-profit"></em></span>
                            <span class="nk-menu-text"> Invoice Outstanding</span>
                        </a>
                    </li><!-- .nk-menu-item -->
                    @endunless
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-histroy"></em></span>
                            <span class="nk-menu-text">History</span>
                        </a>
                        <ul class="nk-menu-sub">
                            <li class="nk-menu-item">
                                <a href="{{ url('/tenant/history/ticket') }}" id="ht" class="nk-menu-link"><span class="nk-menu-text">Ticket</span></a>
                            </li>
                            @unless(session('Tflag') == 'O')
                            <li class="nk-menu-item">
                                <a href="{{ url('/tenant/history/invoice') }}" id="hb" class="nk-menu-link"><span class="nk-menu-text">Invoice</span></a>
                            </li>
                            @endunless
                        </ul><!-- .nk-menu-sub -->
                    </li><!-- .nk-menu-item -->
                    <li class="nk-menu-item">
                        <a href="{{ url('/tenant/news') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-template-fill"></em></span>
                            <span class="nk-menu-text"> News</span>
                        </a>
                    </li><!-- .nk-menu-item -->
                    <li class="nk-menu-item">
                        <a href="{{ url('/tenant/usersurvey/index') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-edit"></em></span>
                            <span class="nk-menu-text"> Online Survey</span>
                        </a>
                    </li><!-- .nk-menu-item -->
                </ul><!-- .nk-menu -->
            </div><!-- .nk-sidebar-menu -->
        </div><!-- .nk-sidebar-content -->
    </div><!-- .nk-sidebar-element -->
</div>