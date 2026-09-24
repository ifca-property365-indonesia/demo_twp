<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Electricity Usage</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: "DejaVu Sans", Helvetica, Arial, sans-serif; font-size: 10px; color: #1f2937; margin: 0; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .muted { color: #6b7280; }
        .chart { text-align: center; margin: 16px 0; }
        .chart img { width: 600px; max-width: 100%; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1d2333; color: #fff; padding: 6px 8px; border: 1px solid #1d2333; font-size: 10px; }
        td { padding: 5px 8px; border: 1px solid #d1d5db; }
        tr:nth-child(even) td { background: #f5f6fa; }
        .text-center { text-align: center; }
        .text-end, .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>Electricity Usage</h1>
    <div class="muted">Generated {{ date('d M Y H:i') }}</div>

    <div class="chart">
        <img src="{{ url('./storage/file_generate/chart/') }}/{{ $image }}" alt="chart">
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">Period</th>
                <th class="text-center">LWBP Usage (kWh)</th>
                <th class="text-center">WBP Usage (kWh)</th>
            </tr>
        </thead>
        <tbody>
            {!! $cl !!}
        </tbody>
    </table>
</body>
</html>
