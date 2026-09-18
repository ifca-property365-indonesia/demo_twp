@extends('tenant.template.base')
@section('content')
<style type="text/css">
    .transbox {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 35%;
        background: rgba(0,0,0,0.5);
        padding-top: 50px;
        padding-left: 50px;
        padding-right:30px;
    }
    </style>
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Dashboard</h3>
                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div><!-- .nk-block-head -->
        <div class="nk-block">
            <?php if(!empty($dtnews)){ 
                $no=1;?>
            <div class="row">
                <div class="col-12">
                    <div id="carouselExCap" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner text-light">
                            <?php foreach ($dtnews as $key) {
                                if($no==1){
                                    $active = 'active';
                                }else{
                                    $active = '';
                                }
                                $string = strip_tags($key->content);

                                if (strlen($string) > 150) {
                                    $stringCut = substr($string, 0, 150);
                                    $endPoint = strrpos($stringCut, ' ');

                                    $string = $endPoint
                                        ? substr($stringCut, 0, $endPoint)
                                        : $stringCut;

                                    $string .= '... <span style="float:right;color:white;text-decoration:underline;">Read More</span>';
                                }

                                echo '<div class="carousel-item '.$active.'"
                                    onclick="window.location.href=\''.url('/tenant/news#news-'.$key->id).'\'"
                                    style="cursor:pointer;">
                                        

                                    <div style="height:300px; display:flex; justify-content:flex-end; align-items:center; background:#000; padding-right:80px;">
                                        <img src="'.$key->picture.'"
                                            style="max-width:100%; max-height:300px; width:auto; height:auto;">
                                    </div>

                                    <div class="transbox">
                                        <h5 style="color:white!important">'.$key->subject.'</h5><br>
                                        <p>'.$string.'</p>
                                    </div>
                                </div>';
                                    $no++;
                            }?>
                          
                        </div>
                        <a class="carousel-control-prev" href="#carouselExCap" role="button" data-slide="prev" style="justify-content: left!important;padding-left:10px">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExCap" role="button" data-slide="next" style="justify-content: right!important;padding-right:10px">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?><br>
            <div class="row">
                @php
                    $colSize = session('Tflag') == 'O' ? 12 : 8;
                @endphp

                <div class="col-{{ $colSize }}">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">
                                        <span class="mr-2" id="utilityTitle">Monthly Utility Usage</span>
                                    </h6>
                                </div>

                                <div class="card-tools d-flex align-items-center" style="gap:10px;">
                                    <select class="form-control" name="yearcombo" id="yearcombo" style="width:150px;">
                                        <?php
                                        $currentYear = date('Y');

                                        for ($i = 0; $i < 5; $i++) {
                                            $year = $currentYear - $i;
                                            $selected = ($year == $currentYear) ? 'selected' : '';

                                            echo "<option value='$year' $selected>$year</option>";
                                        }
                                        ?>
                                    </select>

                                    <select class="form-control" name="utilitycombo" id="utilitycombo" style="width:150px;">
                                        <option value="">-- Select Utility --</option>
                                        <option value="E">Electric</option>
                                        <option value="W">Water</option>
                                        <option value="G">Gas</option>
                                    </select>

                                    <select class="select2 form-control"
                                            name="meteridcombo"
                                            id="meteridcombo"
                                            style="width:300px;"
                                            disabled>
                                        <option value="">-- Select Meter ID --</option>
                                        <?php echo $combometerid; ?>
                                    </select>
                                    
                                </div>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" id="tab1" href="#tabItem1">Area</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" id="tab2" href="#tabItem2">Bar</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tabItem1">
                                    <canvas id="areaChart"></canvas>
                                </div>
                                <div class="tab-pane" id="tabItem2">
                                    <canvas id="barChart"></canvas>
                                </div>
                                <div class="mt-3" id="legendDiv"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @unless(session('Tflag') == 'O')
                <div class="col-4">

                    {{-- CARD PERTAMA --}}
                    <?php if(!$statusPembayaran) { ?>
                    <div class="card card-bordered border-danger mb-3">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">
                                        <span class="mr-2">IMPORTANT NOTIFICATION</span>
                                    </h6>
                                </div>
                            </div>

                            <a href="{{ url('/tenant/proforma') }}" class="badge badge-danger mt-3">
                                You Have Proforma
                            </a>

                            <div class="mt-2">
                                <strong>Total :</strong><br>
                                {!! $totalProforma !!}
                            </div>
                        </div>
                    </div>
                    <?php } else { ?>
                    <div class="card card-bordered border-primary mb-3">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">
                                        <span class="mr-2">PROFORMA NOTIFICATION</span>
                                    </h6>
                                </div>
                            </div>

                            <p class="card-text badge badge-primary mt-3">
                                No Proforma
                            </p>
                        </div>
                    </div>
                    <?php } ?>

                    {{-- CARD PERTAMA --}}
                    <?php if(!$statusInvoice) { ?>
                    <div class="card card-bordered border-danger mb-3">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">
                                        <span class="mr-2">INVOICE NOTIFICATION</span>
                                    </h6>
                                </div>
                            </div>

                            <a href="{{ url('/tenant/invoice') }}" class="badge badge-danger mt-3">
                                You Have Invoice
                            </a>

                            <div class="mt-2">
                                <strong>Total :</strong><br>
                                {!! $totalInvoice !!}
                            </div>
                        </div>
                    </div>
                    <?php } else { ?>
                    <div class="card card-bordered border-primary mb-3">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">
                                        <span class="mr-2">IMPORTANT NOTIFICATION</span>
                                    </h6>
                                </div>
                            </div>

                            <p class="card-text badge badge-primary mt-3">
                                No Invoice
                            </p>
                        </div>
                    </div>
                    <?php } ?>

                </div>
                @endunless
            </div>
            
            <div class="card card-bordered mt-3">
                <div class="card-inner">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title">
                                <span class="mr-2">Our Latest Ticket</span>
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <?php
                            if(!empty($list_hticket)) {
                        ?>
                        <table id="tblTicket" class="table table-bordered table-striped" role="grid" aria-describedby="tblTicket_info">
                            <thead style="background:#101924; color: #ffffff;">
                                <tr role="row">
                                    <th class="sorting text-center" style="width: 7px; vertical-align: middle;">No.</th>
                                    <th class="sorting text-center" style="width: 24px;">Ticket Number</th>
                                    <th class="sorting text-center" style="width: 24px; vertical-align: middle;">Category</th>
                                    <th class="sorting text-center" style="vertical-align: middle;">Description</th>
                                    <th class="sorting text-center" style="width: 100px;">Reported Date</th>
                                    <th class="sorting text-center" style="width: 24px;">Request By</th>
                                    <th class="sorting text-center" style="width: 80px;">Lot No</th>
                                    <th class="sorting text-center" style="width: 10px;">Ticket Type</th>
                                    <th class="sorting text-center" style="width: 10px;">Ticket Status</th>
                                    <th class="text-center" style="width: 80px; vertical-align: middle;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    if (!empty($list_hticket))
                                    { 
                                        echo $list_hticket;
                                    }  
                                ?>
                            </tbody>
                        </table>
                        <?php  
                            } else {
                                echo "<p class='card-text badge badge-gray'>Data Not Available</p>";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block -->
    </div>

    <script type="text/javascript">
        function genPDF()
        {
            var meteridcombo = $("#meteridcombo").find(':selected').val();

            var chart ='';
            if ($('.nav-tabs .active').text() == 'Area')
            {
                chart = document.getElementById("areaChart").toDataURL();
                
            } else {
                chart = document.getElementById("barChart").toDataURL();
            }

            var site_url = "{{ url('tenant/dash/gen') }}";
            $.post(site_url,
            {
                "_token": "{{ csrf_token() }}",
                meteridcombo,
                chart
            },
            function (data, status)
            {
                if (status=='success'){
                    window.open(data);
                } else {
                    Swal.fire({
                        title: "Information",
                        icon:"error",
                        text: "Failed generating pdf file."
                    });
                }
            })
        };

        function changeStatus(id)
        {
            Swal.fire({
                title: 'Cancel this Request Overtime?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then(function(a){
                if (a.value==true)
                {
                    $.ajax({
                        url : "{{ url('/tenant/dash/cancelOT') }}",
                        type:"POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                        },
                        dataType:"json",
                        success:function(event, data)
                        {
                            if (event.status == 'OK')
                            {
                                Swal.fire({
                                    title: "Information",
                                    animation: true,
                                    icon:"success",
                                    text: event.pesan,
                                    confirmButtonText: "OK"
                                }).then(function(){
                                    window.location.reload(true);
                                });
                            } else {
                                Swal.fire({
                                    title: "Information",
                                    animation: true,
                                    icon:"error",
                                    text: event.pesan,
                                    confirmButtonText: "OK"
                                });
                            }
                        },error: function(jqXHR, textStatus, errorThrown){
                            Swal.fire({
                                title: "Error",
                                animation: true,
                                icon:"error",
                                text: textStatus+' Save : '+errorThrown,
                                confirmButtonText: "OK",
                            });
                        }
                    });
                } else {
                }
            })
        }

        $(document).ready(function(){
            

            $('#tblTicket').DataTable({
                paging: false,
                dom: "Bfrtip",
                buttons: [
                    {
                        extend: 'pdf',
                        title: 'Our Latest Ticket',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        className: 'btn btn-primary mb-2',
                        text: '<em class="icon ni ni-download"></em>&nbsp;Generate PDF',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                        },
                        init: function(api, node, config) {
                            $(node).removeClass('dt-button');
                        }
                    }
                ]
            });

            $('.select2').select2();

            var yearcombo = $(this).find('#yearcombo').val();
            var meteridcombo = $(this).find('#meteridcombo').val();
            
            console.log(meteridcombo);
            console.log(yearcombo);

            $.ajax({
                type: 'POST',
                datatType: 'json',
                url: "{{ url('tenant/dash/getGraphMeterId') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    meteridcombo: meteridcombo,
                    yearcombo: yearcombo
                },
                success:function(data){
                    var datas = JSON.parse(data);
                    var aop = {
                        showScale: true,
                        scaleShowGridLines: true,
                        scaleGridLineColor: "rgba(0,0,0,.05)",
                        scaleGridLineWidth: 1,
                        scaleShowHorizontalLines: true,
                        scaleLabel: "<%= value%> kwh",
                        scaleShowVerticalLines: true,
                        bezierCurve: true,
                        bezierCurveTension: 0.3,
                        pointDot: false,
                        pointDotRadius: 4,
                        pointDotStrokeWidth:2,
                        pointHitDetectionRadius: 20,
                        datasetStroke: true,
                        datasetStrokeWidth: 2,
                        datasetFill: false,
                        maintainAspectRatio: false,
                        responsive: true
                    };

                    var bop = {
                        scaleBeginAtZero: true,
                        scaleShowGridLines: true,
                        scaleGridLineColor: "rgba(0,0,0,.05)",
                        scaleGridLineWidth: 1,
                        scaleShowHorizontalLines: true,
                        scaleShowVerticalLines: true,
                        scaleLabel: "<%= value%> kwh",
                        barShowStroke: true,
                        barStrokeWidth: 2,
                        barValueSpacing: 5,
                        barDatasetSpacing: 1,
                        responsive: true,
                        maintainAspectRatio: false
                    };

                    // AREA CHART
                    $("#areaChart").remove();
                    $("#tabItem1").append('<canvas id="areaChart"></canvas>');
                    var cta = document.getElementById("areaChart").getContext("2d");

                    var ach = new Chart(cta, {
                        type: 'line',
                        data: datas.chartdt,
                        options: {
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Usage : ' +
                                                Number(context.raw).toLocaleString(
                                                    'en-US',
                                                    {
                                                        minimumFractionDigits: 2,
                                                        maximumFractionDigits: 2
                                                    }
                                                ) + ' kWh';
                                        },
                                        afterLabel: function(context) {
                                            return '(' + datas.meterid[context.dataIndex] + ')';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    ticks: {
                                        callback: function(value) {
                                            return Number(value).toLocaleString(
                                                'en-US',
                                                {
                                                    minimumFractionDigits: 0,
                                                    maximumFractionDigits: 0
                                                }
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // BAR CHART
                    $("#barChart").remove();
                    $("#tabItem2").append('<canvas id="barChart"></canvas>');
                    var ctb = document.getElementById("barChart").getContext("2d");

                    var bch = new Chart(ctb, {
                        type: 'bar',
                        data: datas.chartdt,
                        options: {
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Usage : ' +
                                                Number(context.raw).toLocaleString(
                                                    'en-US',
                                                    {
                                                        minimumFractionDigits: 2,
                                                        maximumFractionDigits: 2
                                                    }
                                                ) + ' kWh';
                                        },
                                        afterLabel: function(context) {
                                            return '(' + datas.meterid[context.dataIndex] + ')';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    ticks: {
                                        callback: function(value) {
                                            return Number(value).toLocaleString(
                                                'en-US',
                                                {
                                                    minimumFractionDigits: 0,
                                                    maximumFractionDigits: 0
                                                }
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // BUTTON
                    $("#legendDiv").empty();
                    
                }
            });
            

            $('#meteridcombo').change(function() {
                var meteridcombo = $(this).val();
                var yearcombo = $('#yearcombo').val();
                var utility = $('#utilitycombo').val();

                // Jangan load graph kalau belum ada meter
                if (!meteridcombo || !utility) {
                    return;
                }

                $.ajax({
                    type: 'POST',
                    datatType: 'json',
                    url: "{{ url('tenant/dash/getGraphMeterId') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        meteridcombo: meteridcombo,
                        yearcombo: yearcombo,
                        utility: utility
                    },
                    success:function(data){
                        var datas = JSON.parse(data);

                        var aop = {showScale: true, scaleShowGridLines: true, scaleGridLineColor: "rgba(0,0,0,.05)", scaleGridLineWidth: 1, scaleShowHorizontalLines: true, scaleLabel: "<%= value%> kwh", scaleShowVerticalLines: true, bezierCurve: true, bezierCurveTension: 0.3, pointDot: false, pointDotRadius: 4, pointDotStrokeWidth:2, pointHitDetectionRadius: 20, datasetStroke: true, datasetStrokeWidth: 2, datasetFill: false, maintainAspectRatio: false, responsive: true};

                        var bop = {scaleBeginAtZero: true, scaleShowGridLines: true, scaleGridLineColor: "rgba(0,0,0,.05)", scaleGridLineWidth: 1, scaleShowHorizontalLines: true, scaleShowVerticalLines: true, scaleLabel: "<%= value%> kwh", barShowStroke: true, barStrokeWidth: 2, barValueSpacing: 5, barDatasetSpacing: 1, responsive: true, maintainAspectRatio: false };

                        // AREA CHART
                        $("#areaChart").remove();
                        $("#tabItem1").append('<canvas id="areaChart"></canvas>');
                        var cta = document.getElementById("areaChart").getContext("2d");

                        var ach = new Chart(cta, {
                            type: 'line',
                            data: datas.chartdt,
                            options: {
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return 'Usage : ' +
                                                    Number(context.raw).toLocaleString(
                                                        'en-US',
                                                        {
                                                            minimumFractionDigits: 2,
                                                            maximumFractionDigits: 2
                                                        }
                                                    ) + ' kWh';
                                            },
                                            afterLabel: function(context) {
                                                return '(' + datas.meterid[context.dataIndex] + ')';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        ticks: {
                                            callback: function(value) {
                                                return Number(value).toLocaleString(
                                                    'en-US',
                                                    {
                                                        minimumFractionDigits: 0,
                                                        maximumFractionDigits: 0
                                                    }
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        });

                        // BUTTON
                        $("#legendDiv").empty();
                        

                        // BAR CHART
                        $("#barChart").remove();
                        $("#tabItem2").append('<canvas id="barChart"></canvas>');
                        var ctb = document.getElementById("barChart").getContext("2d");

                        var bch = new Chart(ctb, {
                            type: 'bar',
                            data: datas.chartdt,
                            options: {
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return 'Usage : ' +
                                                    Number(context.raw).toLocaleString(
                                                        'en-US',
                                                        {
                                                            minimumFractionDigits: 2,
                                                            maximumFractionDigits: 2
                                                        }
                                                    ) + ' kWh';
                                            },
                                            afterLabel: function(context) {
                                                return '(' + datas.meterid[context.dataIndex] + ')';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        ticks: {
                                            callback: function(value) {
                                                return Number(value).toLocaleString(
                                                    'en-US',
                                                    {
                                                        minimumFractionDigits: 0,
                                                        maximumFractionDigits: 0
                                                    }
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        });

                        $("#legendDiv").empty();
                        
                    }
                });
            });

            $('#yearcombo').change(function() {
                var meteridcombo = $('#meteridcombo').val();
                var yearcombo = $(this).val();
                var utility = $('#utilitycombo').val();
                if (!meteridcombo || !utility) {
                    return;
                }

                $.ajax({
                    type: 'POST',
                    datatType: 'json',
                    url: "{{ url('tenant/dash/getGraphMeterId') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        meteridcombo: meteridcombo,
                        yearcombo: yearcombo,
                        utility: utility
                    },
                    success:function(data){
                        var datas = JSON.parse(data);

                        var aop = {showScale: true, scaleShowGridLines: true, scaleGridLineColor: "rgba(0,0,0,.05)", scaleGridLineWidth: 1, scaleShowHorizontalLines: true, scaleLabel: "<%= value%> kwh", scaleShowVerticalLines: true, bezierCurve: true, bezierCurveTension: 0.3, pointDot: false, pointDotRadius: 4, pointDotStrokeWidth:2, pointHitDetectionRadius: 20, datasetStroke: true, datasetStrokeWidth: 2, datasetFill: false, maintainAspectRatio: false, responsive: true};

                        var bop = {scaleBeginAtZero: true, scaleShowGridLines: true, scaleGridLineColor: "rgba(0,0,0,.05)", scaleGridLineWidth: 1, scaleShowHorizontalLines: true, scaleShowVerticalLines: true, scaleLabel: "<%= value%> kwh", barShowStroke: true, barStrokeWidth: 2, barValueSpacing: 5, barDatasetSpacing: 1, responsive: true, maintainAspectRatio: false };

                        // AREA CHART
                        $("#areaChart").remove();
                        $("#tabItem1").append('<canvas id="areaChart"></canvas>');
                        var cta = document.getElementById("areaChart").getContext("2d");

                        var ach = new Chart(cta, {
                            type: 'line',
                            data: datas.chartdt,
                            options: {
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return 'Usage : ' +
                                                    Number(context.raw).toLocaleString(
                                                        'en-US',
                                                        {
                                                            minimumFractionDigits: 2,
                                                            maximumFractionDigits: 2
                                                        }
                                                    ) + ' kWh';
                                            },
                                            afterLabel: function(context) {
                                                return '(' + datas.meterid[context.dataIndex] + ')';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        ticks: {
                                            callback: function(value) {
                                                return Number(value).toLocaleString(
                                                    'en-US',
                                                    {
                                                        minimumFractionDigits: 0,
                                                        maximumFractionDigits: 0
                                                    }
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        });

                        // BUTTON
                        $("#legendDiv").empty();
                        

                        // BAR CHART
                        $("#barChart").remove();
                        $("#tabItem2").append('<canvas id="barChart"></canvas>');
                        var ctb = document.getElementById("barChart").getContext("2d");

                        var bch = new Chart(ctb, {
                            type: 'bar',
                            data: datas.chartdt,
                            options: {
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return 'Usage : ' +
                                                    Number(context.raw).toLocaleString(
                                                        'en-US',
                                                        {
                                                            minimumFractionDigits: 2,
                                                            maximumFractionDigits: 2
                                                        }
                                                    ) + ' kWh';
                                            },
                                            afterLabel: function(context) {
                                                return '(' + datas.meterid[context.dataIndex] + ')';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        ticks: {
                                            callback: function(value) {
                                                return Number(value).toLocaleString(
                                                    'en-US',
                                                    {
                                                        minimumFractionDigits: 0,
                                                        maximumFractionDigits: 0
                                                    }
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        });

                        $("#legendDiv").empty();
                        
                    }
                });
            });

            function resetCharts() {

                // =========================
                // RESET AREA CHART
                // =========================
                $("#areaChart").remove();
                $("#tabItem1").append('<canvas id="areaChart"></canvas>');

                var cta = document.getElementById("areaChart").getContext("2d");

                new Chart(cta, {
                    type: 'line',
                    data: {
                        labels: [
                            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                        ],
                        datasets: [
                            {
                                label: 'Monthly Usage',
                                data: [
                                    null, null, null, null,
                                    null, null, null, null,
                                    null, null, null, null
                                ],
                                fill: false,
                                tension: 0.3,
                                pointRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        pointDotRadius: 4,
                        pointDotStrokeWidth:2,
                        pointHitDetectionRadius: 20,
                        datasetStroke: true,
                        datasetStrokeWidth: 2,
                        datasetFill: false,
                        scaleLabel: "<%= value%> kwh",
                        scaleShowVerticalLines: true,
                        bezierCurve: true,
                        bezierCurveTension: 0.3,
                        pointDot: false,
                        scaleShowGridLines: true,
                        scaleGridLineColor: "rgba(0,0,0,.05)",
                        scaleGridLineWidth: 1,
                        scaleShowHorizontalLines: true,
                        showScale: true,

                        plugins: {
                            legend: {
                                display: true
                            },
                            tooltip: {
                                enabled: false
                            }
                        },

                        scales: {
                            y: {
                                beginAtZero: true,
                                min: 0,
                                max: 1,
                                ticks: {
                                    stepSize: 0.1,
                                    callback: function(value) {
                                        return value;
                                    }
                                }
                            }
                        }
                    }
                });

                // =========================
                // RESET BAR CHART
                // =========================
                $("#barChart").remove();
                $("#tabItem2").append('<canvas id="barChart"></canvas>');

                var ctb = document.getElementById("barChart").getContext("2d");

                new Chart(ctb, {
                    type: 'bar',
                    data: {
                        labels: [
                            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                        ],
                        datasets: [
                            {
                                label: 'Monthly Usage',
                                data: [
                                    null, null, null, null,
                                    null, null, null, null,
                                    null, null, null, null
                                ]
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,

                        plugins: {
                            legend: {
                                display: true
                            },
                            tooltip: {
                                enabled: false
                            }
                        },

                        scales: {
                            y: {
                                beginAtZero: true,
                                min: 0,
                                max: 1,
                                ticks: {
                                    stepSize: 0.1,
                                    callback: function(value) {
                                        return value;
                                    }
                                }
                            }
                        }
                    }
                });

                // Hapus legend custom
                $("#legendDiv").empty();
            }

            $('#utilitycombo').change(function() {
                updateUtilityTitle();
                var utility = $(this).val();
                var $meter = $('#meteridcombo');

                // TAMBAHKAN INI
                resetCharts();

                // Reset Meter ID
                $meter.empty();
                $meter.append('<option value="">-- Select Meter ID --</option>');

                // Disable jika Utility belum dipilih
                if (utility === '') {
                    $meter.prop('disabled', true);
                    $meter.trigger('change');
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: "{{ url('tenant/dash/getMeterIdByUtility') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        utility: utility
                    },
                    dataType: 'json',

                    success: function(response) {

                        if (response.status) {

                            $meter.append(response.html);

                            $meter.prop('disabled', false);

                        } else {

                            $meter.prop('disabled', true);

                        }

                        $meter.trigger('change');
                    },

                    error: function(xhr) {

                        console.log(xhr.responseText);

                        $meter.prop('disabled', true);
                    }
                });

            });

            function updateUtilityTitle() {

                var utility = $('#utilitycombo').val();

                var title = 'Monthly Utility Usage';

                if (utility === 'E') {
                    title = 'Monthly Electric Usage';
                } 
                else if (utility === 'W') {
                    title = 'Monthly Water Usage';
                } 
                else if (utility === 'G') {
                    title = 'Monthly Gas Usage';
                }

                $('#utilityTitle').text(title);
            }
        })

        
    </script>
@endsection