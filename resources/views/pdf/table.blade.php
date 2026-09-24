{{--
    PDF tabel (dompdf) bersama, tampilannya mengikuti DataTables di layar:
    header gelap #1d2333, baris belang, status berupa badge lembut.

    $title       judul laporan
    $filters     [label => nilai] ditampilkan di bawah judul (opsional)
    $columns     [['label' => ..., 'align' => left|center|right, 'width' => '8%'(opsional)], ...]
    $rows        [[sel, ...], ...]; sel = string, atau ['text' => ..., 'badge' => success|info|warning|...]
    $disclaimer  teks kecil di bawah tabel (opsional)
    Orientasi diatur controller (App\Support\PdfTable).
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 28px 28px 42px 28px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: {{ count($columns) >= 9 ? '8px' : '9px' }}; color: #212631; margin: 0; }
        .head { width: 100%; border-bottom: 2px solid #1d2333; padding-bottom: 6px; margin-bottom: 8px; }
        .head td { vertical-align: bottom; padding: 0; }
        .title { font-size: 15px; font-weight: bold; color: #1d2333; }
        .printed { text-align: right; font-size: 8px; color: #6b7280; }
        .filters { margin-bottom: 8px; font-size: 8.5px; color: #4b5563; }
        .filters span { display: inline-block; margin-right: 14px; }
        .filters b { color: #212631; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data thead { display: table-header-group; }
        table.data tr { page-break-inside: avoid; }
        table.data th { background: #1d2333; color: #fff; font-weight: bold; text-transform: uppercase; font-size: 0.92em; letter-spacing: .02em; padding: 6px 5px; border: 0.5px solid #2c3446; text-align: center; vertical-align: middle; }
        table.data td { padding: 5px; border: 0.5px solid #dee2e6; vertical-align: middle; }
        table.data tbody tr:nth-child(even) td { background: #f5f6f8; }
        .left { text-align: left; } .center { text-align: center; } .right { text-align: right; }
        .empty { text-align: center; color: #6b7280; font-style: italic; padding: 12px; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 0.9em; white-space: nowrap; }
        .badge-primary { background: #eef0ff; color: #3d48b8; }
        .badge-success { background: #e3f8ee; color: #1b7f4a; }
        .badge-info { background: #e3f3fb; color: #1a6e97; }
        .badge-warning { background: #fff5e0; color: #9a6a00; }
        .badge-danger { background: #fde9e7; color: #b42318; }
        .badge-secondary { background: #eceef2; color: #4b5563; }
        .badge-dark { background: #e2e4ea; color: #1f2937; }
        .summary { margin-top: 6px; font-size: 8px; color: #6b7280; }
        .disclaimer { margin-top: 14px; font-size: 7px; color: #9ca3af; text-align: justify; }
    </style>
</head>
<body>
    <table class="head">
        <tr>
            <td class="title">{{ $title }}</td>
            <td class="printed">{{ __('common.pdf_printed_at', ['date' => \Carbon\Carbon::now()->translatedFormat('d F Y H:i')]) }}</td>
        </tr>
    </table>

    @if (!empty($filters))
        <div class="filters">
            @foreach ($filters as $label => $value)
                <span>{{ $label }}: <b>{{ $value }}</b></span>
            @endforeach
        </div>
    @endif

    <table class="data">
        <thead>
            <tr>
                @foreach ($columns as $col)
                    <th @if (!empty($col['width'])) style="width: {{ $col['width'] }}" @endif>{{ $col['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($columns as $i => $col)
                        @php $cell = $row[$i] ?? ''; @endphp
                        <td class="{{ $col['align'] ?? 'left' }}">
                            @if (is_array($cell))
                                <span class="badge badge-{{ $cell['badge'] ?? 'secondary' }}">{{ $cell['text'] }}</span>
                            @else
                                {{ ($cell === null || trim((string) $cell) === '') ? '-' : $cell }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr><td class="empty" colspan="{{ count($columns) }}">{{ __('common.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    @if (!empty($disclaimer))
        <div class="disclaimer">{{ $disclaimer }}</div>
    @endif
    {{-- judul & nomor halaman di kaki tiap halaman ditambahkan App\Support\PdfTable --}}
</body>
</html>
