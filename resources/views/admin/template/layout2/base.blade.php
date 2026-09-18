<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{ url('/images/logoweb/logoweb.png') }}">
    <!-- Page Title  -->
    <title>IFCA Software</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{ url('assets/admin/css/dashlite.css') }}">
    <link id="skin-default" rel="stylesheet" href="{{ url('assets/admin/css/theme.css') }}">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="{{ url('assets/admin/js/bundle.js') }}"></script>
    <script src="{{ url('assets/admin/js/scripts.js') }}"></script>
    <script src="{{ url('assets/admin/js/charts/gd-analytics.js') }}"></script>
    <script src="{{ url('assets/admin/js/libs/jqvmap.js') }}"></script>
    <script src="{{ url('assets/admin/js/example-chart.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>

    <link rel="stylesheet" type="text/css" href="{{ url('assets/admin/css/forms/toggle/switchery.min.css') }}">
    <link href="{{ url('assets/admin/css/plugins/fileupload/css/jquery.fileupload.css') }}" rel="stylesheet" />
    <script src="{{ url('assets/admin/js/forms/icheck/icheck.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/admin/js/forms/toggle/switchery.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/admin/js/forms/toggle/switchery.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/admin/js/forms/switch/switch.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/admin/js/plugins/fileupload/js/jquery.ui.widget.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/admin/js/blockui/jquery.block-ui.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/admin/js/blockui/block-ui.min.js') }}" type="text/javascript"></script>
    <style type="text/css">
        .dataTables_wrapper .dataTables_filter {
            padding-bottom: 10px!important;
        }
    </style>  
</head>

<body class="nk-body npc-default has-apps-sidebar has-sidebar ">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap ">
                @include('admin.template.layout2.header')
                @include('admin.template.layout2.sidebar')
                <!-- content @s -->
                <div class="nk-content">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            @yield('content')
                        </div>
                    </div>
                </div>
                <!-- content @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->

</body>

</html>
