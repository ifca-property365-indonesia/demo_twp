@extends('admin.template.layout2.base')
@section('content')
<style type="text/css">
    .dataTables_filter{
        padding-bottom: 10px!important;
    }
    .table-responsive{
        overflow-x:none!important;
    }
</style>

<div class="nk-content-body">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Dashboard Administrator</h3>
                <div class="nk-block-des text-soft">
                    <p>Welcome to Tenant Web Portal.</p>
                </div>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="card card-preview col-12">
                <div class="card-inner" style="padding-top:10px">
                    <h5 class="card-title" style="border-bottom: solid 2px #dbdfea;padding-bottom:25px;margin-bottom: 20px;">
                        Work Order Graphic
                    </h5>

                    <!-- CHART -->
                    <div style="height: 520px; position: relative;">
                        <canvas
                            class="col-sm-12"
                            id="barChartStatus">
                        </canvas>
                    </div>

                    <br>

                    <!-- TABLE -->
                    <div class="table-responsive">
                        <table class="table table-bordered text-center" style="border-collapse: collapse; width:100%;">
                            <thead>
                                <tr>
                                    <th width="220" style="border:1px solid #d3d3d3; background:#f8f9fc;">
                                        Status
                                    </th>

                                    @foreach(json_decode($labels_status) as $lbl)

                                        <th style="border:1px solid #d3d3d3; background:#f8f9fc;">
                                            {{ $lbl }}
                                        </th>

                                    @endforeach

                                </tr>
                            </thead>

                            <tbody>

    <!-- Submit -->
    <tr>
        <td style="text-align:left;border:1px solid #d3d3d3;padding-left:20px;">
            <span style="display:inline-block;width:14px;height:14px;background:#5B8FF9;margin-right:8px;vertical-align:middle;border-radius:3px;"></span>
            Submit
        </td>

        @foreach(json_decode($submit) as $v)
            <td style="border:1px solid #d3d3d3;">
                {{ $v }}
            </td>
        @endforeach
    </tr>

    <!-- OPEN -->
    <tr>
        <td style="text-align:left;border:1px solid #d3d3d3;padding-left:20px;">
            <span style="display:inline-block;width:14px;height:14px;background:#36CFC9;margin-right:8px;vertical-align:middle;border-radius:3px;"></span>
            Open
        </td>

        @foreach(json_decode($open) as $v)
            <td style="border:1px solid #d3d3d3;">
                {{ $v }}
            </td>
        @endforeach
    </tr>

    <!-- ASSIGNED -->
    <tr>
        <td style="text-align:left;border:1px solid #d3d3d3;padding-left:20px;">
            <span style="display:inline-block;width:14px;height:14px;background:#7C5CFC;margin-right:8px;vertical-align:middle;border-radius:3px;"></span>
            Assigned
        </td>

        @foreach(json_decode($assigned) as $v)
            <td style="border:1px solid #d3d3d3;">
                {{ $v }}
            </td>
        @endforeach
    </tr>

    <!-- PROCESS -->
    <tr>
        <td style="text-align:left;border:1px solid #d3d3d3;padding-left:20px;">
            <span style="display:inline-block;width:14px;height:14px;background:#F5A623;margin-right:8px;vertical-align:middle;border-radius:3px;"></span>
            Process
        </td>

        @foreach(json_decode($process) as $v)
            <td style="border:1px solid #d3d3d3;">
                {{ $v }}
            </td>
        @endforeach
    </tr>

    <!-- CONFIRM -->
    <tr>
        <td style="text-align:left;border:1px solid #d3d3d3;padding-left:20px;">
            <span style="display:inline-block;width:14px;height:14px;background:#1890FF;margin-right:8px;vertical-align:middle;border-radius:3px;"></span>
            Confirm
        </td>

        @foreach(json_decode($confirm) as $v)
            <td style="border:1px solid #d3d3d3;">
                {{ $v }}
            </td>
        @endforeach
    </tr>

    <!-- CLOSED -->
    <tr>
        <td style="text-align:left;border:1px solid #d3d3d3;padding-left:20px;">
            <span style="display:inline-block;width:14px;height:14px;background:#52C41A;margin-right:8px;vertical-align:middle;border-radius:3px;"></span>
            Closed
        </td>

        @foreach(json_decode($closed) as $v)
            <td style="border:1px solid #d3d3d3;">
                {{ $v }}
            </td>
        @endforeach
    </tr>

    <!-- CANCELLED -->
    <tr>
        <td style="text-align:left;border:1px solid #d3d3d3;padding-left:20px;">
            <span style="display:inline-block;width:14px;height:14px;background:#FF4D4F;margin-right:8px;vertical-align:middle;border-radius:3px;"></span>
            Cancelled
        </td>

        @foreach(json_decode($cancelled) as $v)
            <td style="border:1px solid #d3d3d3;">
                {{ $v }}
            </td>
        @endforeach
    </tr>

    <!-- TOTAL -->
    <tr>
        <td style="text-align:left;border:1px solid #d3d3d3;padding-left:20px;font-weight:bold;">
            <span style="display:inline-block;width:14px;height:14px;background:#2F3A4A;margin-right:8px;vertical-align:middle;border-radius:3px;"></span>
            Total
        </td>

        @foreach(json_decode($total) as $v)
            <td style="border:1px solid #d3d3d3;font-weight:bold;">
                {{ $v }}
            </td>
        @endforeach
    </tr>

</tbody>
                        </table>
                    </div>
                </div>
            </div><!-- .card-preview -->
        </div><!-- .row -->
    </div><!-- .nk-block -->
    <br><br>
    <div class="nk-block" id="electricUsage">
        <div class="row g-gs">
            <div class="card card-preview col-12">
                <div class="card-inner" style="padding-top:10px">
                    <h5 class="card-title"
                        style="border-bottom: solid 2px #dbdfea;padding-bottom:25px;margin-bottom:20px;">
                        Electric Usage
                    </h5>

                    <div class="row mb-3">

                        <!-- YEAR -->
                        <div class="col-md-3">
                            <label for="usageYear">Year</label>
                            <select id="usageYear" class="form-control">
                                @foreach($usage_years as $year)
                                    <option value="{{ $year }}"
                                        {{ $selected_year == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- MONTH -->
                        <div class="col-md-3">
                            <label for="usageMonth">Month</label>
                            <select id="usageMonth" class="form-control">
                                @foreach($usage_months as $monthNo => $monthName)
                                    <option value="{{ $monthNo }}"
                                        {{ $selected_month == $monthNo ? 'selected' : '' }}>
                                        {{ $monthName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- CATEGORY -->
                        <div class="col-md-3">
                            <label for="usageCategory">Category</label>

                            <select id="usageCategory" class="form-control">
                                <option value="W">Water</option>
                                <option value="G">Gas</option>
                                <option value="E" selected>Electric</option>
                            </select>
                        </div>

                    </div>

                    <div style="height: 240px!important;">
                        <canvas
                            class="col-sm-12"
                            id="barChartUsage">
                        </canvas>
                    </div>

                    <div class="card-footer">
                        <div id="legendUsage"></div>
                    </div>
                </div>
            </div><!-- .card-preview -->
        </div><!-- .row -->
    </div><!-- .nk-block -->
    <br><br>
    <br><br>
    <div class="nk-block">
        <div class="row g-gs">
            <div class="card card-preview col-12">
                <div class="card-inner" style="padding-top:10px">
                    <h5 class="card-title" style="border-bottom: solid 2px #dbdfea;padding-bottom:12px;margin-bottom: 20px;">Ticket</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tbltickett" width="100%" data-auto-responsive="false">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Ticket Number</th>
                                    <th>Category</th>
                                    <th>Tenant Name</th>
                                    <th>Description</th>
                                    <th>Reported Date</th>
                                    <th>Request By</th>
                                    <th>Lot No</th>
                                    <th>Ticket Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div><!-- .card-preview -->
        </div><!-- .row -->
    </div><!-- .nk-block -->
</div>
<script src="{{ url('assets/admin/js/Chart.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">

$(function() {

    /*
    |--------------------------------------------------------------------------
    | GRAPH 1 : ELECTRIC USAGE
    |--------------------------------------------------------------------------
    */

    var usageChart = null;

    var usageOptions = {

        scaleBeginAtZero: true,
        scaleShowGridLines: true,
        scaleGridLineColor: "rgba(0,0,0,.05)",
        scaleGridLineWidth: 1,
        scaleShowHorizontalLines: true,
        scaleShowVerticalLines: true,
        barShowStroke: true,
        barStrokeWidth: 2,
        barValueSpacing: 5,
        barDatasetSpacing: 1,
        scaleLabel: "<%= value%> kwh",

        legendTemplate:
            '<table class="table table-borderless">' +
            '<% for(var i=0; i<datasets.length; i++) { %>' +
            '<tr>' +
            '<td width="20">' +
            '<div style="width:15px;height:15px;background:<%=datasets[i].fillColor%>"></div>' +
            '</td>' +
            '<td><%= datasets[i].label %></td>' +
            '</tr>' +
            '<% } %>' +
            '</table>',

        responsive: true,
        maintainAspectRatio: false
    };

    usageOptions.datasetFill = false;

    var usageChart = null;

    function loadUsageChart(year, month, category)
    {
        $.ajax({
            url: "{{ url('/admin/dash/data/usage') }}",
            type: "POST",

            data: {
                year: year,
                month: month,
                category: category,
                _token: "{{ csrf_token() }}"
            },

            success: function(response)
            {
                console.log('Category:', category);
                console.log('Response:', response);

                // Hapus chart lama
                if (usageChart) {
                    usageChart.destroy();
                    usageChart = null;
                }

                var usageData = {
                    labels: response.labels,
                    datasets: [
                        {
                            label:"LWBP (Lewat Waktu Beban Puncak) 22:00 - 17:00",
                            fillColor: "rgba(60,141,188,0.9)",
                            strokeColor: "rgba(60,141,188,0.8)",
                            pointColor: "#3b8bba",
                            pointStrokeColor: "rgba(60,141,188,1)",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(60,141,188,1)",
                            data: response.usage
                        },
                        {
                            label:"WBP (Waktu Beban Puncak) 17:00 - 22:00",
                            fillColor: "#00a65a",
                            strokeColor: "#00a65a",
                            pointColor: "#00a65a",
                            pointStrokeColor: "#00a65a",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "#00a65a",
                            data: response.usage_high
                        }
                    ]
                };

                var canvas = $("#barChartUsage").get(0);
                var ctx = canvas.getContext("2d");

                usageChart = new Chart(ctx).Bar(
                    usageData,
                    usageOptions
                );

                document.getElementById("legendUsage").innerHTML =
                    usageChart.generateLegend();
            },

            error: function(xhr)
            {
                console.log(xhr.responseText);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD INITIAL ELECTRIC USAGE
    |--------------------------------------------------------------------------
    */

    loadUsageChart(
        $('#usageYear').val(),
        $('#usageMonth').val(),
        $('#usageCategory').val()
    );

    /*
    |--------------------------------------------------------------------------
    | FILTER ELECTRIC USAGE
    |--------------------------------------------------------------------------
    */

    $('#usageYear, #usageMonth, #usageCategory').change(function()
    {

        var year = $('#usageYear').val();
        var month = $('#usageMonth').val();
        var category = $('#usageCategory').val();

        loadUsageChart(year, month, category);

    });

    /*
    |--------------------------------------------------------------------------
    | GRAPH 2 : SERVICE STATUS
    |--------------------------------------------------------------------------
    */

    var labelsStatus = {!! $labels_status !!};
    var submit = {!! $submit !!};
    var open = {!! $open !!};
    var assigned = {!! $assigned !!};
    var process = {!! $process !!};
    var confirmm = {!! $confirm !!};
    var closedd = {!! $closed !!};
    var cancelled = {!! $cancelled !!};
    var total = {!! $total !!};

    var statusData = {
        labels: labelsStatus,
        datasets: [

    {
        label: "Submit",
        fillColor: "#5B8FF9",
        strokeColor: "#5B8FF9",
        pointColor: "#5B8FF9",
        data: submit
    },

    {
        label: "Open",
        fillColor: "#36CFC9",
        strokeColor: "#36CFC9",
        pointColor: "#36CFC9",
        data: open
    },

    {
        label: "Assigned",
        fillColor: "#7C5CFC",
        strokeColor: "#7C5CFC",
        pointColor: "#7C5CFC",
        data: assigned
    },

    {
        label: "Process",
        fillColor: "#F5A623",
        strokeColor: "#F5A623",
        pointColor: "#F5A623",
        data: process
    },

    {
        label: "Confirm",
        fillColor: "#1890FF",
        strokeColor: "#1890FF",
        pointColor: "#1890FF",
        data: confirmm
    },

    {
        label: "Closed",
        fillColor: "#52C41A",
        strokeColor: "#52C41A",
        pointColor: "#52C41A",
        data: closedd
    },

    {
        label: "Cancelled",
        fillColor: "#FF4D4F",
        strokeColor: "#FF4D4F",
        pointColor: "#FF4D4F",
        data: cancelled
    },

    {
        label: "Total",
        fillColor: "#2F3A4A",
        strokeColor: "#2F3A4A",
        pointColor: "#2F3A4A",
        data: total
    }

]
    };

    var statusCanvas = $("#barChartStatus").get(0).getContext("2d");

    var maxStatusValue = 0;

    statusData.datasets.forEach(function(dataset) {

        dataset.data.forEach(function(value) {

            value = parseInt(value) || 0;

            if (value > maxStatusValue) {
                maxStatusValue = value;
            }

        });

    });

    var statusMax = maxStatusValue + 1;

    var statusOptions = {

        scaleOverride: true,
        scaleSteps: statusMax,
        scaleStepWidth: 1,
        scaleStartValue: 0,
        
        scaleBeginAtZero: true,
        scaleShowGridLines: true,
        scaleGridLineColor: "rgba(0,0,0,.05)",
        scaleGridLineWidth: 1,
        scaleShowHorizontalLines: true,
        scaleShowVerticalLines: true,

        barShowStroke: true,
        barStrokeWidth: 2,

        // Tambahkan jarak antar bar
        barValueSpacing: 10,
        barDatasetSpacing: 4,

        responsive: true,
        maintainAspectRatio: false,

        showTooltips: false,

        animation: false,

        onAnimationComplete: function () {

            var ctx = this.chart.ctx;

            ctx.font = "bold 11px Arial";
            ctx.fillStyle = "#000";
            ctx.textAlign = "center";
            ctx.textBaseline = "bottom";

            var datasets = this.datasets;

            for (var i = 0; i < datasets.length; i++) {

                for (var j = 0; j < datasets[i].bars.length; j++) {

                    var bar = datasets[i].bars[j];

                    /*
                    * Jangan sampai angka keluar dari area chart.
                    * Minimal posisi angka 15px dari atas.
                    */
                    var labelY = Math.max(bar.y - 5, 15);

                    ctx.fillText(
                        bar.value,
                        bar.x,
                        labelY
                    );
                }
            }
        }
    };

    statusOptions.datasetFill = false;

    var statusChart = new Chart(statusCanvas).Bar(
        statusData,
        statusOptions
    );
});

$('#generate').click(function(){

    var site_url = '{{ url("admin/dash/dlpdf")}}';

    $.post(
        site_url,
        {
            file: document.getElementById("barChartUsage").toDataURL(),
            "_token": "{{ csrf_token() }}"
        },
        function(data,status) {

            if(status=='success'){

                window.open(data);

            }else{

                Swal.fire({
                    title: "Information",
                    icon:"error",
                    text: "Failed generating pdf file."
                });

            }
        }
    );

});

</script>

<script text="javascript">
var tblovertime,tblticket;
  $(function() {
    $('.select2').select2();
    tblovertime = $('#tblovertimee').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            "url" : "{{ url('/admin/dash/data/overtime') }}",
            "type": "POST",
            data: {
              "_token": "{{ csrf_token() }}"
              }
            },
            columns: [
              { data: 'row_number', name: 'row_number' },
              { data: 'lot_no', name: 'lot_no' },
              { data: 'tenant_no', name: 'tenant_no' },
              { data:"start_overtime",name:"start_overtime", sortable: true,
                    render:function (data,type,row) {
                            var a = data.substr(0, 4);
                            var b = data.substr(5, 2);
                            var c = data.substr(8, 2);
                            return c+"-"+b+"-"+a;
                        }
              },
              { data:"end_overtime",name:"end_overtime", sortable: true,
                    render:function (data,type,row) {
                            var a = data.substr(0, 4);
                            var b = data.substr(5, 2);
                            var c = data.substr(8, 2);
                            return c+"-"+b+"-"+a;
                        }
              },
              { data: 'status', name: 'status',
              render:function (data,type,row) {
                            var status='',label='';
                            console=data
                            if(data=='N'){
                                status = "Waiting to be activated";
                                label = "info";
                            }else if(data=='A'){
                                status = "Activated";
                                label = "success";
                            }else if(data=="X"){
                                status = "Canceled";
                                label = "warning";
                            }else if(data=="Z"){
                                status = "Posted";
                                label = "danger";	
                            }
                            return '<span class="badge badge-pill badge-'+label+'">'+status+'</span>';
                        } 
                },
              { data: 'description', name: 'description' },
          ],
          dom: '<"toolbar group">frtip',
          "responsive": {
            details: {
                type: 'column',
                target: 8
            }
          }
      });
      tblticket = $('#tbltickett').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            "url" : "{{ url('/admin/dash/data/ticket') }}",
            "type": "POST",
            data: {
              "_token": "{{ csrf_token() }}"
              }
            },
          
          columns: [
              { data: 'row_number', name: 'row_number' },
              { data: 'complain_no', name: 'complain_no' },
              { data: 'categoryname', name: 'categoryname' },
              { data: 'name', name: 'name' },
              { data: 'work_requested', name: 'work_requested' },
              { data:"reported_date",name:"reported_date", sortable: true,
                    render:function (data,type,row) {
                            var a = data.substr(0, 4);
                            var b = data.substr(5, 2);
                            var c = data.substr(8, 2);
                            return c+"-"+b+"-"+a;
                        }
              },
              { data: 'serv_req_by', name: 'serv_req_by' },
              { data: 'lot_no', name: 'lot_no' },
              { data: 'status', name: 'status',
              render:function (data,type,row) {
                            var status='',label='';
                            if(data=='O'){
                                status = "Open";
                                label = "info";
                            }else if(data=='R'){
                                status = "Submit";
                                label = "warning";
                            }else if(data=='A'){
                                status = "Assigned";
                                label = "warning";
                            }else if(data=="Y"){
                                status = "Approve";
                                label = "success";
                            }else if(data=='C'){
                                status = "Close";
                                label = "danger";
                            }else if(data=="X"){
                                status = "Cancel";
                                label = "default";  
                            } else if (data == "S") {
                                status = "Survey";
                                label = "info";
                            } else if (data == "P") {
                                status = "Process";
                                label = "info";
                            }else if (data == "M") {
                                status = "Modify";
                                label = "info";
                            } else if (data == "Z") {
                                status = "Charged Approved";
                                label = "info";
                            } else if (data == "F") {
                                status = "Confirm";
                                label = "info";
                            }
                            return '<span class="badge badge-pill badge-'+label+'">'+status+'</span>';
                        } 
                },
          ],
          dom: '<"toolbar group">frtip',
          "responsive": {
            details: {
                type: 'column',
                target: 8
            }
          }
      });
  });
</script>
@endsection
