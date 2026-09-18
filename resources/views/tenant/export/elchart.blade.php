<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <title>IFCA</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{ url('assets/tenant/css/dashlite.css?ver=2.2.0') }}">
    <link id="skin-default" rel="stylesheet" href="{{ url('assets/tenant/css/theme.css?ver=2.2.0') }}">

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="{{ url('assets/tenant/js/bundle.js?ver=2.2.0') }}"></script>
    <script src="{{ url('assets/tenant/js/scripts.js?ver=2.2.0') }}"></script>
</head>

<body class="nk-body bg-lighter npc-general">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                <div class="nk-block-head nk-block-head-sm">
                                    <div class="nk-block-between">
                                        <div class="nk-block-head-content">
                                            <h3 class="nk-block-title page-title">Export</h3>
                                        </div><!-- .nk-block-head-content -->
                                    </div><!-- .nk-block-between -->
                                </div><!-- .nk-block-head -->
                                <div class="nk-block">
                                    <div class="card card-bordered mt-3">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">
                                                        <span class="mr-2">Electricity Usage</span>
                                                    </h6>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mt-3">
                                                <center>
                                                    <img id="chart" style="width: 600px;" src="{{ url('./storage/file_generate/chart/') }}/{{$image}}" />
                                                </center>
                                            </div>
                                            <hr>
                                            <div class="table-responsive mt-3">
                                                <table id="tblBilling" class="table table-bordered table-striped" role="grid" aria-describedby="tblBilling_info">
                                                    <thead style="background:#101924; color: #ffffff;">
                                                        <tr role='row'>
                                                            <th class="text-center">Periode</th>
                                                            <th class="text-center">LWBP Usage (kwh)</th>
                                                            <th class="text-center">WBP Usage (kwh)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php echo $cl;?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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