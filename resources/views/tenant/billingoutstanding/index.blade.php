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
            <div class="card card-bordered mt-3">
                <div class="card-inner">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title">
                                <span class="mr-2">Billing Outstanding</span>
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <?php
                            if(!empty($list_bill)) {
                        ?>
                        <table id="tblBilling" class="table table-bordered table-striped" role="grid" aria-describedby="tblBilling_info">
                            <thead style="background:#101924; color: #ffffff;">
                                <tr role='row'>
                                    <th class="sorting text-center" style="width: 7px; vertical-align: middle;">No.</th>
                                    <th class="sorting text-center" style="width: 24px;">Document Number</th>
                                    <th class="sorting text-center" style="width: 100px;">Doc Date</th>
                                    <th class="sorting text-center" style="width: 100px;">Due Date</th>
                                    <th class="sorting text-center" style="vertical-align: middle;">Description</th>
                                    <th class="sorting text-center" style="width: 110px; vertical-align: middle;">Periode</th>
                                    <th class="sorting text-center" style="width: 1px; vertical-align: middle;">Currency</th>
                                    <th class="sorting text-center" style="vertical-align: middle;">Outstanding</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    if (!empty($list_bill))
                                    { 
                                        echo $list_bill;
                                    }  
                                ?>
                            </tbody>
                            <tfoot>
                                <?php 
                                    if (!empty($footer_bill))
                                    {
                                        echo $footer_bill;
                                    }  
                                ?>
                            </tfoot>
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
            var lot_no = $("#lotno").find(':selected').val();
            console.log(lot_no);

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
                lot_no,
                chart
            },
            function (data, status)
            {
                console.log(data);
                console.log(status);
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
            console.log(id);
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
            $('#tblBilling').DataTable({
                paging: false,
                dom : "Bfrtip",
                buttons: [
                    {
                        extend: 'pdf',
                        title: 'Billing Outstanding',
                        className: 'btn btn-primary mb-2',
                        text: '<em class="icon ni ni-download"></em>&nbsp;Generate PDF',
                        init: function(api, node, config) {
                            $(node).removeClass('dt-button')
                        },
                    },
                ]
            });

            $('#tblTicket').DataTable({
                paging: false,
                dom : "Bfrtip",
                buttons: [
                    {
                        extend: 'pdf',
                        title: 'Our Latest Ticket',
                        className: 'btn btn-primary mb-2',
                        text: '<em class="icon ni ni-download"></em>&nbsp;Generate PDF',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6, 7]
                        },
                        init: function(api, node, config) {
                            $(node).removeClass('dt-button')
                        },
                    },
                ]
            });

            $('#tblOvertime').DataTable({
                paging: false,
                dom : "Bfrtip",
                buttons: [
                    {
                        extend: 'pdf',
                        title: 'Our Latest Overtime',
                        className: 'btn btn-primary mb-2',
                        text: '<em class="icon ni ni-download"></em>&nbsp;Generate PDF',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6]
                        },
                        init: function(api, node, config) {
                            $(node).removeClass('dt-button')
                        },
                    },
                ]
            });

            $('.select2').select2();

            var lot_no = $(this).find(':selected').val();    

            $.ajax({
                type: 'POST',
                datatType: 'json',
                url: "{{ url('tenant/dash/getGraph') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    lot_no: lot_no
                },
                success:function(data){
                    var datas = JSON.parse(data);

                    // ================== TAMBAHAN WARNA ==================
                    datas.chartdt.datasets.forEach(ds => {
                        if (ds.label && ds.label.includes('22:00 - 18:00')) {
							ds.backgroundColor = 'rgba(189, 168, 112, 0.4)';
							ds.borderColor = 'rgba(189, 168, 112, 1)';
							ds.pointBackgroundColor = 'rgba(189, 168, 112, 1)';
						} 
                        else if (ds.label && ds.label.includes('18:00 - 22:00')) {
                            ds.backgroundColor = 'rgba(255, 193, 7, 0.4)'; // kuning
                            ds.borderColor = 'rgba(255, 193, 7, 1)';
                            ds.pointBackgroundColor = 'rgba(255, 193, 7, 1)';
                        }
                    });
                    // ====================================================

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
                            elements: {
                                line: {
                                    fill: true
                                }
                            },
                            tooltips: {
                                callbacks: {
                                    afterLabel: function(tooltipItem, data) {
                                        return '(' + datas.meterid[tooltipItem['index']] + ')';
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
                            tooltips: {
                                callbacks: {
                                    afterLabel: function(tooltipItem, data) {
                                        return '(' + datas.meterid[tooltipItem['index']] + ')';
                                    }
                                }
                            }
                        }
                    });

                    // BUTTON
                    $("#legendDiv").empty();
                    $("#legendDiv").append('<button name="genPDF" type="button" class="btn btn-primary" onclick="genPDF()"><em class="icon ni ni-download"></em><span>Generate PDF</span></button>');
                }
            });
            

            $('#lotno').change(function() {
                var lot_no = $(this).find(':selected').val();

                $.ajax({
                    type: 'POST',
                    datatType: 'json',
                    url: "{{ url('tenant/dash/getGraph') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        lot_no: lot_no
                    },
                    success:function(data){
                        var datas = JSON.parse(data);

                        // ================== TAMBAHAN WARNA ==================
                        datas.chartdt.datasets.forEach(ds => {
                            if (ds.label && ds.label.includes('22:00 - 18:00')) {
                                ds.backgroundColor = 'rgba(255, 0, 0, 0.4)'; // merah
                                ds.borderColor = 'rgba(255, 0, 0, 1)';
                                ds.pointBackgroundColor = 'rgba(255, 0, 0, 1)';
                            } 
                            else if (ds.label && ds.label.includes('18:00 - 22:00')) {
                                ds.backgroundColor = 'rgba(255, 193, 7, 0.4)'; // kuning
                                ds.borderColor = 'rgba(255, 193, 7, 1)';
                                ds.pointBackgroundColor = 'rgba(255, 193, 7, 1)';
                            }
                        });
                        // ====================================================

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
                                elements: {
                                    line: {
                                        fill: true
                                    }
                                },
                                tooltips: {
                                    callbacks: {
                                        afterLabel: function(tooltipItem, data) {
                                            return '(' + datas.meterid[tooltipItem['index']] + ')';
                                        }
                                    }
                                }
                            }
                        });

                        // BUTTON
                        $("#legendDiv").empty();
                        $("#legendDiv").append('<button name="genPDF" id="genPDF" type="button" class="btn btn-primary" onclick="genPDF()"><em class="icon ni ni-download"></em><span>Generate PDF</span></button>');

                        // BAR CHART
                        $("#barChart").remove();
                        $("#tabItem2").append('<canvas id="barChart"></canvas>');
                        var ctb = document.getElementById("barChart").getContext("2d");

                        var bch = new Chart(ctb, {
                            type: 'bar',
                            data: datas.chartdt,
                            options: {
                                tooltips: {
                                    callbacks: {
                                        afterLabel: function(tooltipItem, data) {
                                            return '(' + datas.meterid[tooltipItem['index']] + ')';
                                        }
                                    }
                                }
                            }
                        });

                        // BUTTON (lagi, sesuai script kamu)
                        $("#legendDiv").empty();
                        $("#legendDiv").append('<button name="genPDF" id="genPDF" type="button" class="btn btn-primary" onclick="genPDF()"><em class="icon ni ni-download"></em><span>Generate PDF</span></button>');
                    }
                });
            });
        })
    </script>
@endsection