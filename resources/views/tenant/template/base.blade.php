<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{ url('img/logoweb/logoweb.png') }}">
    <!-- Page Title  -->
    <title>IFCA</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{ url('assets/tenant/css/dashlite.css?ver=2.2.0') }}">
    <link id="skin-default" rel="stylesheet" href="{{ url('assets/tenant/css/theme.css?ver=2.2.0') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="{{ url('assets/tenant/js/bundle.js?ver=2.2.0') }}"></script>
    <script src="{{ url('assets/tenant/js/scripts.js?ver=2.2.0') }}"></script>
    <script src="{{ url('assets/tenant/js/charts/gd-default.js?ver=2.2.0') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.2/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
    <style>
        .spinner-overlay {
            display: none;
            position: fixed;
            z-index: 99999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);

            align-items: center;
            justify-content: center;
        }

        .spinner-box {
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 30px rgba(0,0,0,.3);
        }

        .spinner {
            width: 45px;
            height: 45px;
            border: 5px solid #ddd;
            border-top: 5px solid #007bff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto;
        }

        .loading-text {
            margin-top: 15px;
            font-size: 15px;
            color: #333;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="nk-body bg-lighter npc-general has-sidebar ">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            @include('tenant.template.sidebar')
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                @include('tenant.template.header')
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            @yield('content')
                        </div>
                    </div>
                </div>
                <!-- content @e -->
                <!-- footer @s -->
                @include('tenant.template.footer')
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
</body>

</html>

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
            </div>
        </div>
    </div>
</div>

<!-- Modal Content Code -->
<div class="modal fade" tabindex="-1" role="dialog" id="modallg">
    <div id="modaldialoglg" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header" id="modalheaderlg">
                <h5 class="modal-title" id="modaltitlelg"></h5>
            </div>
            <div class="modal-body" id="modalbodylg">

            </div>
            <div class="modal-footer bg-light" id="modalfooterlg">
            </div>
        </div>
    </div>
</div>