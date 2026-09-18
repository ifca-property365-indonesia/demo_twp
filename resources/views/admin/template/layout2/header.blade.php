<!-- main header @s -->
<div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ml-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
            </div>
            @php
                $data = '';
                $email = Session::get('Tsemail');
                $data = DB::connection('ifcaadm')->select("SELECT * FROM all_login where email = '$email'");
                $pict = $data[0]->pict;
                $useremail = $data[0]->email;
                $username = $data[0]->name;
                if(empty($pict)){
                    $pict = url('/images/User/defaultuser.png');
                }
            @endphp
            <div class="nk-header-app-name">
                <!-- Logo -->
                <div class="nk-header-app-logo" style="margin-right: 10px;">
                    <img src="{{ url('/images/logoweb/logoweb.png') }}" alt="Logo" style="height:40px;">
                </div>
                <div class="nk-header-app-info">
                    <span class="sub-text">Web Admin</span>
                    <span class="lead-text" style="font-size: 18px">Tenant Web Portal</span>
                </div>
            </div>
           
            
            <div class="nk-header-tools">
                <ul class="nk-quick-nav">
                   
                    <li class="dropdown list-apps-dropdown d-lg-none">
                        <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-toggle="dropdown">
                            <div class="icon-status icon-status-na"><em class="icon ni ni-menu-circled"></em></div>
                        </a>
                       
                    </li>

                    <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle mr-n1" data-toggle="dropdown">
                            <div class="user-toggle">
                                <div class="user-avatar sm">
                                    <img src="{{ $pict }}" alt="">
                                </div>
                                <div class="user-info d-none d-xl-block">
                                    <div class="user-status">{{ $username }}</div>
                                    <div class="user-name dropdown-indicator">Administrator</div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
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
                            @include('partials.portal_switch', ['current' => 'admin'])
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li><a href="#" id="profile"><em class="icon ni ni-user-alt"></em><span>View Profile</span></a></li>
                                    <li><a href="{{ url('/admin/logout') }}"><em class="icon ni ni-signout"></em><span>Sign out</span></a></li>
                                </ul>
                            </div>
                            
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- main header @e -->

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

<!-- Modal Content Code -->
<div class="modal fade" tabindex="-1" role="dialog" id="modalsm">
    <div id="modaldialogsm" class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header" id="modalheadersm">
                <h5 class="modal-title" id="modaltitlesm">Modal Title</h5>
            </div>
            <div class="modal-body" id="modalbodysm">

            </div>
            <div class="modal-footer bg-light" id="modalfootersm">
                <button type="button" class="btn btn-primary" id="savefrm-sm">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal XL-->
<div class="modal fade" tabindex="-1" role="dialog" id="modalxl">
    <div id="modaldialogxl" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header" id="modalheaderxl">
                <h5 class="modal-title" id="modaltitlexl">Modal Title</h5>
            </div>
            <div class="modal-body" id="modalbodyxl">

            </div>
            <div class="modal-footer bg-light" id="modalfooterxl">
                <button type="button" class="btn btn-primary" id="savefrmxl">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#profile').click(function(){
    var data = "<?php echo Session::get('Tsemail');?>";
        $('#modaldialog').addClass('modal-md');
        $('#modaltitle').addClass('white');
        $('#modaltitle').html('Edit Profile');
        $('#modalbody').load("{{ url('admin/account/profile') }}");
        $('#modal').data('Id', data);
        $('#modal').modal('show');
        $('.modal-footer').hide();
    });
    function block(boelan,div){
        var load ="{{ url('/images/load.gif') }}";
        var block_ele = $(div);
        if (boelan==true) {
            $(block_ele).block({
                message: '<div class="semibold"><img src="'+load+'" witdh="25px" height="25px">&nbsp; Loading ...</div>',
                fadeIn: 1000,
                fadeOut: 1000,
                overlayCSS: {
                    backgroundColor: '#fff',
                    opacity: 0.8,
                    cursor: 'wait'
                },
                css: {
                    border: 0,
                    padding: '10px 15px',
                    color: '#fff',
                    width: 'auto',
                    backgroundColor: '#333',
                    marginLeft : 'auto'
                }
            });
        }
        else{
            $(block_ele).unblock()
        }
    }
    function FormatDateNew(data){
    if (data==null || data=='') {
      return 'Not Set'
    }
    var date = new Date(data.replace(/\s/, 'T'));
    var dd = date.getDate();
    var mm = date.getMonth() + 1
    var yyyy = date.getFullYear();
    var h = date.getHours();
    var m = date.getMinutes();
    if (dd < 10) {
      dd = '0' + dd;
    } 
    if (mm < 10) {
      mm = '0' + mm;
    }

    var newdate = dd + '-' + mm + '-' + yyyy ;

    return newdate
  }
    function FormatDateTimeNew(date) {
        if(date==''||date==null){
        return 'Not Set';
        }else{
        var dd = new Date(date.replace(/\s/, 'T'));
        var dt = dd.getDate();
        var Mn = dd.getMonth() + 1;
        var Yr = dd.getFullYear();

        var Hr = dd.getHours();
        var Mnt = dd.getMinutes();
        if(dt < 10){
            dt ='0'+dt;
        }
        if(Mn < 10){
            Mn ='0'+Mn;
        }
   
        if (Hr < 10) {
            Hr = '0' + Hr;
        } 
        if (Mnt < 10) {
            Mnt = '0' + Mnt;
        } 

        return dt +'-'+Mn+'-'+Yr+' '+Hr+':'+Mnt;
        }
        
    }
   
</script>
