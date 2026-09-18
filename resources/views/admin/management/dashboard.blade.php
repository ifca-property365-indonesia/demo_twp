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
                        Aging AP Graphic
                    </h5>
                    <div class="card">
                        <div class="card-body">
                            <div style="height:500px">
                                <div id="apChart" style="height:500px;"></div>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
            </div><!-- .card-preview -->
        </div><!-- .row -->
    </div><!-- .nk-block -->
    <br><br>
    <div class="nk-block">
        <div class="row g-gs">
            <div class="card card-preview col-12">
                <div class="card-inner" style="padding-top:10px">
                    <h5 class="card-title" style="border-bottom: solid 2px #dbdfea;padding-bottom:25px;margin-bottom: 20px;">
                        Aging AR Graphic
                    </h5>
                    <div class="card">
                        <div class="card-body">
                            <div style="height:500px">
                                <div id="arChart" style="height:500px;"></div>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
            </div><!-- .card-preview -->
        </div><!-- .row -->
    </div><!-- .nk-block -->
    <br><br>
    <div class="nk-block">
        <div class="row g-gs">
            <div class="card card-preview col-12">
                <div class="card-inner" style="padding-top:10px">
                    
                    <div class="d-flex justify-content-between align-items-center" style="border-bottom: solid 2px #dbdfea; padding-bottom:15px; margin-bottom: 20px;">
                        <h5 class="card-title mb-0">
                            REVENUE
                        </h5>
                        <div style="width: 150px;">
                            <select id="yearFilterRevenue" class="form-control form-select">
                                </select>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div id="revenueChart" style="height:400px;"></div>
                            <br>
                            <div id="dataTableContainerRevenue" style="width: 100%; margin-top: -15px; padding-bottom: 10px;"></div>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <div class="nk-block">
        <div class="row g-gs">
            <div class="card card-preview col-12">
                <div class="card-inner" style="padding-top:10px">
                    
                    <div class="d-flex justify-content-between align-items-center" style="border-bottom: solid 2px #dbdfea; padding-bottom:15px; margin-bottom: 20px;">
                        <h5 class="card-title mb-0">
                            EXPENSE
                        </h5>
                        <div style="width: 150px;">
                            <select id="yearFilterExpense" class="form-control form-select">
                                </select>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div id="expenseChart" style="height:400px;"></div>
                            <br>
                            <div id="dataTableContainerExpense" style="width: 100%; margin-top: -15px; padding-bottom: 10px;"></div>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    
</div>
<script src="<?php echo e(url('assets/admin/js/Chart.min.js')); ?>" type="text/javascript"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>

<script>

$(document).ready(function () {

    let currentYear = new Date().getFullYear();

    // Tangkap kedua elemen dropdown
    let yearFilterRevenue = $('#yearFilterRevenue');
    let yearFilterExpense = $('#yearFilterExpense');

    // 1. Generate opsi tahun untuk KEDUA dropdown
    for (let i = 0; i < 20; i++) {
        let y = currentYear - i;
        
        // Buat elemen option baru
        let optionHtml = `<option value="${y}">${y}</option>`;
        
        // Masukkan ke masing-masing dropdown
        yearFilterRevenue.append(optionHtml);
        yearFilterExpense.append(optionHtml);
    }

    // 2. Event Listener khusus untuk Chart Revenue
    yearFilterRevenue.on('change', function() {
        let selectedYear = $(this).val();
        loadRevenueChart(selectedYear); // Hanya meload ulang Revenue
    });

    // 3. Event Listener khusus untuk Chart Expense
    yearFilterExpense.on('change', function() {
        let selectedYear = $(this).val();
        loadExpenseChart(selectedYear); // Hanya meload ulang Expense
    });

    // 4. Load chart yang tidak butuh filter tahun
    loadApChart();
    loadArChart();

    // 5. Load kedua chart pertama kali dengan tahun saat ini
    loadRevenueChart(currentYear);
    loadExpenseChart(currentYear);

});

function formatCurrency(value)
{
    value = Number(value);

    if (value >= 1000000000000) {
        return (value / 1000000000000).toFixed(2) + 'T';
    }

    if (value >= 1000000000) {
        return (value / 1000000000).toFixed(2) + 'B';
    }

    if (value >= 1000000) {
        return (value / 1000000).toFixed(2) + 'M';
    }

    if (value >= 1000) {
        return (value / 1000).toFixed(2) + 'K';
    }

    return value.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function renderArTable(response)
{
    var html = '';

    for(var i = 0; i < response.length; i += 5)
    {
        var rowColor = (Math.floor(i / 5) % 2 === 0)
            ? '#f8f9fc'
            : '#eef2ff';

        html += '<tr>';

        for(var j = 0; j < 5; j++)
        {
            var idx = i + j;

            if(idx < response.length)
            {
                html += '<td style="background:'+ rowColor +'; border:1px solid #d3d3d3;">'
                        + response[idx].debtor_acct +
                        '</td>';

                html += '<td style="background:'+ rowColor +'; border:1px solid #d3d3d3; text-align:right;">'
                        + formatCurrency(response[idx].amount) +
                        '</td>';
            }
            else
            {
                html += '<td style="background:'+ rowColor +'; border:1px solid #d3d3d3;"></td>';
                html += '<td style="background:'+ rowColor +'; border:1px solid #d3d3d3;"></td>';
            }
        }

        html += '</tr>';
    }

    $('#arTable tbody').html(html);
}

function loadArChart()
{
    $.ajax({
        url: "<?php echo e(url('admin/management/ar-aging')); ?>",
        type: "GET",
        dataType: "json",

        success: function(response)
        {
            renderArTable(response);

            var categories = [];
            var amounts = [];

            $.each(response, function(i, row) {

                categories.push(row.name);

                // convert ke Billion (Bn)
                amounts.push(
                    parseFloat(row.amount) / 1000000000
                );

            });

            Highcharts.chart('arChart', {

                accessibility: {
                    enabled: false // <--- Tambahkan kode ini untuk menghilangkan warning
                },
                
                chart: {
                    type: 'bar'
                },

                title: {
                    text: 'AR Aging Profile (IDRbn)'
                },

                xAxis: {
                    categories: categories,
                    title: {
                        text: null
                    }
                },

                yAxis: {
                    min: 0,
                    title: {
                        text: null
                    },
                    labels: {
                        formatter: function() {
                            return this.value.toFixed(3);
                        }
                    }
                },

                legend: {
                    enabled: false
                },

                credits: {
                    enabled: false
                },

                tooltip: {
                    pointFormatter: function() {
                        return '<b>' + Highcharts.numberFormat(this.y, 3) + '</b>';
                    }
                },

                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return Highcharts.numberFormat(this.y, 3);
                            }
                        }
                    }
                },

                series: [{
                    name: 'AR',
                    color: '#2f6f8f',
                    data: amounts
                }]
            });
        }
    });
}

function renderApTable(response)
{
    var html = '';

    for(var i = 0; i < response.length; i += 5)
    {
        var rowColor = (Math.floor(i / 5) % 2 === 0)
            ? '#f8f9fc'
            : '#eef2ff';

        html += '<tr>';

        for(var j = 0; j < 5; j++)
        {
            var idx = i + j;

            if(idx < response.length)
            {
                html += '<td style="background:'+ rowColor +'; border:1px solid #d3d3d3;">'
                        + response[idx].creditor_acct +
                        '</td>';

                html += '<td style="background:'+ rowColor +'; border:1px solid #d3d3d3; text-align:right;">'
                        + formatCurrency(response[idx].amount) +
                        '</td>';
            }
            else
            {
                html += '<td style="background:'+ rowColor +'; border:1px solid #d3d3d3;"></td>';
                html += '<td style="background:'+ rowColor +'; border:1px solid #d3d3d3;"></td>';
            }
        }

        html += '</tr>';
    }

    $('#apTable tbody').html(html);
}

function loadApChart()
{
    $.ajax({
        url: "<?php echo e(url('admin/management/ap-aging')); ?>",
        type: "GET",
        dataType: "json",

        success: function(response)
        {
            renderApTable(response);

            var categories = [];
            var amounts = [];

            $.each(response, function(i, row) {

                categories.push(row.name);

                // convert ke Billion (Bn)
                amounts.push(
                    parseFloat(row.amount) / 1000000000
                );

            });

            Highcharts.chart('apChart', {

                accessibility: {
                    enabled: false // <--- Tambahkan kode ini untuk menghilangkan warning
                },
                
                chart: {
                    type: 'bar'
                },

                title: {
                    text: 'AP Aging Profile (IDRbn)'
                },

                xAxis: {
                    categories: categories,
                    title: {
                        text: null
                    }
                },

                yAxis: {
                    min: 0,
                    title: {
                        text: null
                    },
                    labels: {
                        formatter: function() {
                            return this.value.toFixed(3);
                        }
                    }
                },

                legend: {
                    enabled: false
                },

                credits: {
                    enabled: false
                },

                tooltip: {
                    pointFormatter: function() {
                        return '<b>' + Highcharts.numberFormat(this.y, 3) + '</b>';
                    }
                },

                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return Highcharts.numberFormat(this.y, 3);
                            }
                        }
                    }
                },

                series: [{
                    name: 'AP',
                    color: '#2f6f8f',
                    data: amounts
                }]
            });
        }
    });
}

function loadRevenueChart(year) {
    $.ajax({
        url: "<?php echo e(url('admin/management/revenue-data')); ?>",
        type: "GET",
        data: { year: year }, //baru tambah
        dataType: "json",
        success: function(response) {
            var categories = [
                'JAN','FEB','MAR','APR','MAY','JUN',
                'JUL','AUG','SEP','OCT','NOV','DEC'
            ];

            var actuals = [];
            var budgets = [];

            $.each(response, function(i, row){
                actuals.push(parseFloat(row.actual || 0));
                budgets.push(parseFloat(row.budget || 0));
            });

            // 1. Inisialisasi Highcharts
            Highcharts.chart('revenueChart', {
                accessibility: {
                    enabled: false // <--- Tambahkan kode ini untuk menghilangkan warning
                },
                chart: {
                    type: 'column',
                    marginLeft: 220, // Disinkronkan dengan lebar kolom pertama tabel
                    marginRight: 15,
                    marginBottom: 20 // Kurangi margin bawah agar menempel ke tabel
                },
                title: {
                    text: 'REVENUE ' + year // <--- Akan berubah otomatis jadi REVENUE 2025, dsb.
                },
                xAxis: {
                    categories: categories,
                    labels: { enabled: false }, // Sembunyikan label X bawaan
                    tickLength: 0,
                    lineWidth: 0
                },
                yAxis: {
                    min: 0,
                    title: { text: null },
                    labels: {
                        formatter: function () {
                            // Format 30,000.0 sesuai gambar 2
                            return Highcharts.numberFormat(this.value, 1, '.', ','); 
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: false // Legend dimatikan karena informasinya ada di tabel
                },
                tooltip: {
                    shared: true,
                    pointFormatter: function() {
                        return '<span style="color:' + this.color + '">●</span> ' +
                            this.series.name + ': <b>' +
                            Highcharts.numberFormat(this.y, 1, '.', ',') +
                            '</b><br/>';
                    }
                },
                plotOptions: {
                    column: {
                        pointPadding: 0,   // Membuat bar saling menempel (tidak ada jarak dalam 1 grup)
                        groupPadding: 0.1, // Jarak antar bulan dibuat lebih sempit
                        borderWidth: 0,
                        dataLabels: { enabled: false }
                    }
                },
                series: [
                    {
                        name: 'TOTAL REVENUE ACTUAL',
                        data: actuals,
                        color: '#4F81BD',
                    },
                    {
                        name: 'TOTAL REVENUE BUDGET',
                        data: budgets,
                        color: '#C0504D',
                    }
                ]
            });

            // 2. Build HTML Data Table Dinamis
            let tableHTML = `<table style="width: 100%; table-layout: fixed; border-collapse: collapse; font-family: 'Segoe UI', sans-serif; font-size: 12px; text-align: center; color: #666;">`;

            // Baris 1: Header Bulan
            tableHTML += `<tr><td style="width: 220px; border: none;"></td>`; // Kosong di pojok kiri (lebar sama dengan marginLeft chart)
            $.each(categories, function(i, cat) {
                tableHTML += `<td style="border: 1px solid #ccc; padding: 5px;">${cat}</td>`;
            });
            tableHTML += `</tr>`;

            // Baris 2: Data Actual
            tableHTML += `<tr>
                <td style="border: 1px solid #ccc; text-align: left; padding: 5px 10px;">
                    <span style="color:#4F81BD; margin-right:5px;">■</span> TOTAL REVENUE ACTUAL
                </td>`;
            $.each(actuals, function(i, val) {
                // Menampilkan kosong jika 0, atau format angka "21,428.8"
                let txt = val > 0 ? Highcharts.numberFormat(val, 1, '.', ',') : '';
                tableHTML += `<td style="border: 1px solid #ccc; padding: 5px;">${txt}</td>`;
            });
            tableHTML += `</tr>`;

            // Baris 3: Data Budget
            tableHTML += `<tr>
                <td style="border: 1px solid #ccc; text-align: left; padding: 5px 10px;">
                    <span style="color:#C0504D; margin-right:5px;">■</span> TOTAL REVENUE BUDGET
                </td>`;
            $.each(budgets, function(i, val) {
                let txt = val > 0 ? Highcharts.numberFormat(val, 1, '.', ',') : '';
                tableHTML += `<td style="border: 1px solid #ccc; padding: 5px;">${txt}</td>`;
            });
            tableHTML += `</tr></table>`;

            // Render tabel ke dalam container
            $('#dataTableContainerRevenue').html(tableHTML);
        }
    });
}

function loadExpenseChart(year) {
    $.ajax({
        url: "<?php echo e(url('admin/management/expense-data')); ?>",
        type: "GET",
        data: { year: year }, //baru tambah
        dataType: "json",
        success: function(response) {
            var categories = [
                'JAN','FEB','MAR','APR','MAY','JUN',
                'JUL','AUG','SEP','OCT','NOV','DEC'
            ];

            var actuals = [];
            var budgets = [];

            $.each(response, function(i, row){
                actuals.push(parseFloat(row.actual || 0));
                budgets.push(parseFloat(row.budget || 0));
            });

            // 1. Inisialisasi Highcharts
            Highcharts.chart('expenseChart', {
                accessibility: {
                    enabled: false // <--- Tambahkan kode ini untuk menghilangkan warning
                },
                chart: {
                    type: 'column',
                    marginLeft: 220, // Disinkronkan dengan lebar kolom pertama tabel
                    marginRight: 15,
                    marginBottom: 20 // Kurangi margin bawah agar menempel ke tabel
                },
                title: {
                    text: 'EXPENSE ' + year // <--- Akan berubah otomatis jadi REVENUE 2025, dsb.
                },
                xAxis: {
                    categories: categories,
                    labels: { enabled: false }, // Sembunyikan label X bawaan
                    tickLength: 0,
                    lineWidth: 0
                },
                yAxis: {
                    min: 0,
                    title: { text: null },
                    labels: {
                        formatter: function () {
                            // Format 30,000.0 sesuai gambar 2
                            return Highcharts.numberFormat(this.value, 1, '.', ','); 
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: false // Legend dimatikan karena informasinya ada di tabel
                },
                tooltip: {
                    shared: true,
                    pointFormatter: function() {
                        return '<span style="color:' + this.color + '">●</span> ' +
                            this.series.name + ': <b>' +
                            Highcharts.numberFormat(this.y, 1, '.', ',') +
                            '</b><br/>';
                    }
                },
                plotOptions: {
                    column: {
                        pointPadding: 0,   // Membuat bar saling menempel (tidak ada jarak dalam 1 grup)
                        groupPadding: 0.1, // Jarak antar bulan dibuat lebih sempit
                        borderWidth: 0,
                        dataLabels: { enabled: false }
                    }
                },
                series: [
                    {
                        name: 'TOTAL EXPENSE ACTUAL',
                        data: actuals,
                        color: '#4F81BD',
                    },
                    {
                        name: 'TOTAL EXPENSE BUDGET',
                        data: budgets,
                        color: '#C0504D',
                    }
                ]
            });

            // 2. Build HTML Data Table Dinamis
            let tableHTML = `<table style="width: 100%; table-layout: fixed; border-collapse: collapse; font-family: 'Segoe UI', sans-serif; font-size: 12px; text-align: center; color: #666;">`;

            // Baris 1: Header Bulan
            tableHTML += `<tr><td style="width: 220px; border: none;"></td>`; // Kosong di pojok kiri (lebar sama dengan marginLeft chart)
            $.each(categories, function(i, cat) {
                tableHTML += `<td style="border: 1px solid #ccc; padding: 5px;">${cat}</td>`;
            });
            tableHTML += `</tr>`;

            // Baris 2: Data Actual
            tableHTML += `<tr>
                <td style="border: 1px solid #ccc; text-align: left; padding: 5px 10px;">
                    <span style="color:#4F81BD; margin-right:5px;">■</span> TOTAL EXPENSE ACTUAL
                </td>`;
            $.each(actuals, function(i, val) {
                // Menampilkan kosong jika 0, atau format angka "21,428.8"
                let txt = val > 0 ? Highcharts.numberFormat(val, 1, '.', ',') : '';
                tableHTML += `<td style="border: 1px solid #ccc; padding: 5px;">${txt}</td>`;
            });
            tableHTML += `</tr>`;

            // Baris 3: Data Budget
            tableHTML += `<tr>
                <td style="border: 1px solid #ccc; text-align: left; padding: 5px 10px;">
                    <span style="color:#C0504D; margin-right:5px;">■</span> TOTAL EXPENSE BUDGET
                </td>`;
            $.each(budgets, function(i, val) {
                let txt = val > 0 ? Highcharts.numberFormat(val, 1, '.', ',') : '';
                tableHTML += `<td style="border: 1px solid #ccc; padding: 5px;">${txt}</td>`;
            });
            tableHTML += `</tr></table>`;

            // Render tabel ke dalam container
            $('#dataTableContainerExpense').html(tableHTML);
        }
    });
}

</script>
@endsection