@extends('admin.template.layout2.base')

@section('title', 'Dashboard')

@push('styles')
<style>
    .status-table td:first-child { text-align: left; }
    .status-swatch { display: inline-block; width: 12px; height: 12px; border-radius: 3px; margin-right: 8px; vertical-align: middle; }
    .chart-box { position: relative; }
</style>
@endpush

@section('content')
@php
    $statusRows = [
        ['Submit',    '#5B8FF9', $submit],
        ['Open',      '#36CFC9', $open],
        ['Assigned',  '#7C5CFC', $assigned],
        ['Process',   '#F5A623', $process],
        ['Confirm',   '#1890FF', $confirm],
        ['Closed',    '#52C41A', $closed],
        ['Cancelled', '#FF4D4F', $cancelled],
        ['Total',     '#2F3A4A', $total],
    ];
    $statusJson = [];
    foreach ($statusRows as $r) {
        $statusJson[] = ['label' => $r[0], 'color' => $r[1], 'data' => json_decode($r[2])];
    }
@endphp
<div class="page-body">
    <div class="page-head">
        <div class="page-head-row">
            <div class="page-head-content">
                <h3 class="page-title">Dashboard Administrator</h3>
                <div class="page-desc"><p>Welcome to Tenant Web Portal.</p></div>
            </div>
        </div>
    </div>

    {{-- Work order graphic --}}
    <div class="card mb-4">
        <div class="card-header fw-bold">Work Order Graphic</div>
        <div class="card-body">
            <div class="chart-box" style="height: 420px;">
                <canvas id="barChartStatus"></canvas>
            </div>
            <div class="table-responsive mt-4">
                <table class="table table-bordered text-center table-sm-text status-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 220px;">Status</th>
                            @foreach (json_decode($labels_status) as $lbl)
                                <th>{{ $lbl }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($statusRows as [$label, $color, $values])
                            <tr class="{{ $label === 'Total' ? 'fw-bold' : '' }}">
                                <td><span class="status-swatch" style="background: {{ $color }};"></span>{{ $label }}</td>
                                @foreach (json_decode($values) as $v)
                                    <td>{{ $v }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Utility usage --}}
    <div class="card mb-4" id="electricUsage">
        <div class="card-header fw-bold">Utility Usage</div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-sm-4 col-md-3">
                    <label for="usageYear" class="form-label">Year</label>
                    <select id="usageYear" class="form-select">
                        @foreach ($usage_years as $year)
                            <option value="{{ $year }}" {{ $selected_year == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4 col-md-3">
                    <label for="usageMonth" class="form-label">Month</label>
                    <select id="usageMonth" class="form-select">
                        @foreach ($usage_months as $monthNo => $monthName)
                            <option value="{{ $monthNo }}" {{ $selected_month == $monthNo ? 'selected' : '' }}>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4 col-md-3">
                    <label for="usageCategory" class="form-label">Category</label>
                    <select id="usageCategory" class="form-select">
                        <option value="W">Water</option>
                        <option value="G">Gas</option>
                        <option value="E" selected>Electric</option>
                    </select>
                </div>
            </div>
            <div class="chart-box" style="height: 300px;">
                <canvas id="barChartUsage"></canvas>
            </div>
        </div>
    </div>

    {{-- Tickets --}}
    <div class="card">
        <div class="card-header fw-bold">Ticket</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered w-100" id="tbltickett">
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
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
$(function () {
    // Plugin kecil: tulis nilai di atas tiap bar
    var barValuePlugin = {
        id: 'barValues',
        afterDatasetsDraw: function (chart) {
            var ctx = chart.ctx;
            ctx.save();
            ctx.font = 'bold 11px sans-serif';
            ctx.fillStyle = '#1f2937';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            chart.data.datasets.forEach(function (dataset, i) {
                var meta = chart.getDatasetMeta(i);
                if (meta.hidden) { return; }
                meta.data.forEach(function (bar, j) {
                    var v = dataset.data[j];
                    if (v === null || v === undefined || v === '') { return; }
                    ctx.fillText(v, bar.x, Math.max(bar.y - 4, 12));
                });
            });
            ctx.restore();
        }
    };

    var fmt = function (v) { return Number(v).toLocaleString('en-US', { maximumFractionDigits: 2 }); };

    /* ---------------- Work order status ---------------- */
    var statusRows = {!! json_encode($statusJson) !!};

    new Chart(document.getElementById('barChartStatus'), {
        type: 'bar',
        plugins: [barValuePlugin],
        data: {
            labels: {!! $labels_status !!},
            datasets: statusRows.map(function (r) {
                return { label: r.label, backgroundColor: r.color, borderColor: r.color, borderWidth: 1, data: r.data.map(function (v) { return parseInt(v) || 0; }) };
            })
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: { legend: { position: 'bottom' }, tooltip: { enabled: true } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    /* ---------------- Utility usage ---------------- */
    var usageChart = null;
    var UNIT = { E: 'kWh', W: 'm³', G: 'm³' };

    function loadUsageChart() {
        var category = $('#usageCategory').val();

        $.ajax({
            url: "{{ url('/admin/dash/data/usage') }}",
            type: 'POST',
            data: { year: $('#usageYear').val(), month: $('#usageMonth').val(), category: category }
        }).done(function (response) {
            if (usageChart) { usageChart.destroy(); }

            var datasets = [{
                label: category === 'E' ? 'LWBP (Lewat Waktu Beban Puncak) 22:00 - 17:00' : 'Usage',
                backgroundColor: 'rgba(79, 91, 213, .85)',
                borderColor: 'rgba(79, 91, 213, 1)',
                borderWidth: 1,
                data: response.usage || []
            }];
            if (category === 'E') {
                datasets.push({
                    label: 'WBP (Waktu Beban Puncak) 17:00 - 22:00',
                    backgroundColor: 'rgba(46, 184, 92, .85)',
                    borderColor: 'rgba(46, 184, 92, 1)',
                    borderWidth: 1,
                    data: response.usage_high || []
                });
            }

            usageChart = new Chart(document.getElementById('barChartUsage'), {
                type: 'bar',
                data: { labels: response.labels || [], datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { callbacks: { label: function (c) { return c.dataset.label + ': ' + fmt(c.raw) + ' ' + (UNIT[category] || ''); } } }
                    },
                    scales: { y: { beginAtZero: true, ticks: { callback: fmt } } }
                }
            });
        }).fail(function (xhr) {
            console.log(xhr.responseText);
        });
    }

    loadUsageChart();
    $('#usageYear, #usageMonth, #usageCategory').on('change', loadUsageChart);

    /* ---------------- Ticket table ---------------- */
    var STATUS = {
        O: ['Open', 'info'], R: ['Submit', 'warning'], A: ['Assigned', 'warning'], Y: ['Approve', 'success'],
        C: ['Close', 'danger'], X: ['Cancel', 'secondary'], S: ['Survey', 'info'], P: ['Process', 'info'],
        M: ['Modify', 'info'], Z: ['Charged Approved', 'info'], F: ['Confirm', 'info']
    };
    var ymdToDmy = function (d) { return d ? d.substr(8, 2) + '-' + d.substr(5, 2) + '-' + d.substr(0, 4) : ''; };

    $('#tbltickett').DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: "{{ url('/admin/dash/data/ticket') }}", type: 'POST' },
        columns: [
            { data: 'row_number', name: 'row_number', orderable: false, searchable: false },
            { data: 'complain_no', name: 'complain_no' },
            { data: 'categoryname', name: 'categoryname' },
            { data: 'name', name: 'name' },
            { data: 'work_requested', name: 'work_requested' },
            { data: 'reported_date', name: 'reported_date', render: ymdToDmy },
            { data: 'serv_req_by', name: 'serv_req_by' },
            { data: 'lot_no', name: 'lot_no' },
            { data: 'status', name: 'status', render: function (d) {
                var s = STATUS[d] || [d, 'secondary'];
                return '<span class="badge rounded-pill badge-soft-' + s[1] + '">' + s[0] + '</span>';
            } }
        ]
    });
});
</script>
@endpush
