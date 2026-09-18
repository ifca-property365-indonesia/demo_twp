<div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ml-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
            </div>
            <div class="nk-header-brand d-xl-none">
                <div class="nk-header-app-info">
                    <span class="sub-text">WEB TENANT</span>
                    <span class="lead-text">Tenant Web Portal</span>
                </div>
            </div><!-- .nk-header-brand -->
            @php
                $email = Session::get('Tenemail');

                $data = DB::table('tenant')
                    ->where('email', $email)
                    ->first();

                $portalType = '';

                if ($data) {
                    if ($data->flag == 'O') {
                        $portalType = 'Operational';
                    }
                }
            @endphp

            <div class="nk-header-news d-none d-xl-block">
                <div class="nk-news-list">
                    <div class="nk-header-app-info">
                        <span class="sub-text">WEB TENANT</span>
                        <span class="lead-text">
                            Tenant Web Portal{{ $portalType ? " ({$portalType})" : '' }}
                        </span>
                    </div>
                </div>
            </div>
            @php
             
                $data = '';
                $email = Session::get('Tenemail');
                $data = DB::select("SELECT * FROM all_login where email = '$email'");
                $pict = $data[0]->pict;
                $useremail = $data[0]->email;
                $username = $data[0]->name;
                if(empty($pict)){
                    $pict = url('/images/User/defaultuser.png');
                }
            @endphp
            <div class="nk-header-tools">
                <ul class="nk-quick-nav">
                    <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <div class="user-toggle">
                                <div class="user-avatar">
                                    <img src="{{ $pict }}" alt="">
                                </div>
                                <div class="user-info">
                                    <span class="lead-text">{{ $username }}</span>
                                    <span class="sub-text">{{ $useremail }}</span>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right dropdown-menu-s1">
                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                <div class="user-card">
                                    <div class="user-avatar">
                                        <img src="{{ $pict }}" alt="">
                                    </div>
                                    <div class="user-info">
                                        <span class="lead-text">{{ $username }}</span>
                                        <span class="sub-text">{{ $useremail }}</span>
                                    </div>
                                </div>
                            </div>
                            @include('partials.portal_switch', ['current' => 'tenant'])
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li><a id="profile" style="cursor: pointer;"><em class="icon ni ni-user-alt"></em><span>View Profile</span></a></li>
                                </ul>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li><a href="{{ url('tenant/logout')}}"><em class="icon ni ni-signout"></em><span>Sign out</span></a></li>
                                </ul>
                            </div>
                            
                        </div>
                    </li><!-- .dropdown -->
                </ul><!-- .nk-quick-nav -->
            </div><!-- .nk-header-tools -->
        </div><!-- .nk-header-wrap -->
    </div><!-- .container-fliud -->
</div>
<!-- Modal Content Code -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header" id="modalheader">
                <h5 class="modal-title" id="modaltitle">Modal Title</h5>
            </div>
            <div class="modal-body" id="modalbody">

            </div>
            <div class="modal-footer bg-light" id="modalfooter">
                <button type="button" class="btn btn-primary" id="savefrm">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#profile').click(function(){
        var data = "<?php echo Session::get('Tenemail');?>";
        $('#modaldialog').addClass('modal-md');
        $('#modaltitle').addClass('white');
        $('#modaltitle').html('Edit Profile');
        $('#modalbody').load("{{ url('tenant/account/profile') }}");
        $('#modal').data('Id', data);
        $('#modal').modal('show');
        $('.modal-footer').hide();
    });
</script>