<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $header->complain_no }}</title>
    <style type="text/css">
        @page { margin: 18px 20px; }
        body { font-family: "DejaVu Sans", Helvetica, Arial, sans-serif; font-size: 9.5px; color: #1f3d5c; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        .sheet { border: 1px solid #1f3d5c; }
        .pad { padding: 6px 10px; }
        .line { border-bottom: 1px solid #1f3d5c; }
        .val { color: #d98032; }
        .lbl { color: #1f3d5c; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .title { font-size: 12px; font-weight: bold; color: #0f5f57; }
        .bar { background: #e9eef3; border-top: 1px solid #1f3d5c; border-bottom: 1px solid #1f3d5c;
               font-weight: bold; padding: 4px 10px; color: #1f3d5c; }
        .box { width: 9px; height: 9px; border: 1px solid #1f3d5c; display: inline-block;
               text-align: center; line-height: 9px; font-size: 8px; margin-right: 6px; }
        .grid td { border-right: 1px solid #1f3d5c; }
        .grid td.last { border-right: 0; }
        .items th { background: #e9eef3; border: 1px solid #1f3d5c; padding: 4px 6px; font-size: 9px; }
        .items td { border: 1px solid #1f3d5c; padding: 4px 6px; }
        .sign { height: 62px; }
        .row-line td { padding: 3px 0; }
    </style>
</head>
<body>

@php
    $fdate = function ($value, $format = 'd M Y') {
        return $value ? date($format, strtotime($value)) : '-';
    };
    $txt = function ($value) {
        $value = trim((string) $value);
        return $value === '' ? '-' : $value;
    };
@endphp

<table class="sheet">
    <!-- kop: nomor dokumen -->
    <tr>
        <td class="pad right">
            <div><span class="lbl bold">Doc No. : </span><span class="val">{{ $header->complain_no }}</span></div>
            <div><span class="lbl bold">Date : </span><span class="val">{{ $fdate($header->audit_date, 'd M Y H:i') }}</span></div>
            <div><span class="lbl bold">Create By : </span><span class="val">{{ $txt($detail->member_name ?? $header->serv_req_by) }}</span></div>
        </td>
    </tr>

    <!-- judul -->
    <tr>
        <td class="pad center line" style="padding-bottom: 14px;">
            <div class="title" style="margin-top: 10px;">{{ strtoupper($txt($tenancy->entity_desc ?? '')) }}</div>
            <div class="title">{{ $title }}</div>
            <div class="title">({{ $short }})</div>
            <div class="lbl" style="margin-top: 6px;">{{ $txt($tenancy->project_desc ?? '') }}</div>
        </td>
    </tr>

    <!-- identitas pemohon -->
    <tr>
        <td class="line" style="padding: 0;">
            <table class="grid">
                <tr>
                    <td class="pad" style="width: 50%;">
                        <table class="row-line">
                            <tr>
                                <td class="lbl" style="width: 95px;">From</td>
                                <td class="val">: {{ $txt($tenant->name ?? $header->debtor_acct) }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Tower / Floor / Unit</td>
                                <td class="val">: {{ $txt($detail->tower ?? '') }} / {{ $txt($detail->floor ?? $header->floor) }} / {{ $txt($detail->unit ?? $header->lot_no) }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Requested By</td>
                                <td class="val">: {{ $txt($detail->member_name ?? $header->serv_req_by) }}
                                    @if (trim((string) ($detail->member_hp ?? $header->contact_no)) !== '')
                                        / {{ trim($detail->member_hp ?? $header->contact_no) }}
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="pad last" style="width: 50%;">
                        <table class="row-line">
                            <tr>
                                <td class="lbl" style="width: 80px;">Category</td>
                                <td class="val">: {{ ucwords(strtolower($title)) }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Source</td>
                                <td class="val">: {{ $txt($header->complain_source) }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Tenant</td>
                                <td class="val">: {{ $txt($header->debtor_acct) }} - {{ $txt($tenant->name ?? '') }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- deskripsi -->
    <tr><td class="bar center">Description of Works</td></tr>
    <tr>
        <td class="pad line val" style="height: 58px;">{{ $txt($header->note) }}</td>
    </tr>

    <!-- jenis permit & detail -->
    <tr>
        <td class="line" style="padding: 0;">
            <table class="grid">
                <tr>
                    <td class="pad" style="width: 50%;">
                        <div class="lbl bold" style="margin-bottom: 8px;">Type of Permit :</div>
                        <table class="row-line">
                            @foreach ($types as $code => $label)
                                <tr>
                                    <td style="width: 20px;"><span class="box">{{ $type === $code ? 'X' : '' }}</span></td>
                                    <td class="lbl">{{ $label }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                    <td class="pad last" style="width: 50%;">
                        <div class="lbl bold" style="margin-bottom: 8px;">Detail :</div>
                        <table class="row-line">
                            @if ($type === 'W')
                                <tr>
                                    <td class="lbl" style="width: 95px;">Person In Charge</td>
                                    <td class="val">: {{ $txt($detail->pic_name ?? $header->pj_name) }}</td>
                                </tr>
                                <tr>
                                    <td class="lbl">Contractor</td>
                                    <td class="val">: {{ $txt($detail->kontraktor_name ?? $header->contractor) }}</td>
                                </tr>
                                <tr>
                                    <td class="lbl">Type of Work</td>
                                    <td class="val">: {{ $txt($detail->work_type ?? $header->job_type) }}</td>
                                </tr>
                                <tr>
                                    <td class="lbl">Work Tools</td>
                                    <td class="val">: {{ $txt($detail->work_tools ?? $header->work_tool) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td class="lbl" style="width: 95px;">Company</td>
                                    <td class="val">: {{ $txt($detail->company_name ?? $header->company_name) }}</td>
                                </tr>
                                <tr>
                                    <td class="lbl">Owner</td>
                                    <td class="val">: {{ $txt($detail->owner_name ?? $header->owner_name) }}</td>
                                </tr>
                                <tr>
                                    <td class="lbl">Vehicle No</td>
                                    <td class="val">: {{ $txt($detail->vehicle_no ?? $header->vehicle_no) }}</td>
                                </tr>
                            @endif
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- daftar pekerja / barang -->
    <tr><td class="bar">{{ $type === 'W' ? 'Worker List' : 'Item List' }}</td></tr>
    <tr>
        <td class="pad line">
            <table class="items">
                <tr>
                    <th style="width: 30px;">No.</th>
                    <th>{{ $lines_title }}</th>
                </tr>
                @forelse ($lines as $i => $name)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td class="val">{{ $txt($name) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="center" colspan="2">No data</td>
                    </tr>
                @endforelse
            </table>
        </td>
    </tr>

    <!-- jadwal -->
    <tr>
        <td class="line" style="padding: 0;">
            <table class="grid">
                <tr>
                    <td class="pad center" style="width: 33%;">
                        <div class="lbl bold" style="margin-bottom: 14px;">Permit Start</div>
                        <div class="val">Date : {{ $fdate($header->start_date) }}</div>
                        @if ($type === 'W')<div class="val">Time : {{ $txt($header->start_time) }}</div>@endif
                    </td>
                    <td class="pad center" style="width: 33%;">
                        <div class="lbl bold" style="margin-bottom: 14px;">Permit End</div>
                        <div class="val">Date : {{ $fdate($header->end_date) }}</div>
                        @if ($type === 'W')<div class="val">Time : {{ $txt($header->end_time) }}</div>@endif
                    </td>
                    <td class="pad center last">
                        <div class="lbl bold" style="margin-bottom: 14px;">Status</div>
                        <div class="val">{{ $status_label }}</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- tanda tangan (satu blok saja) -->
    <tr>
        <td style="padding: 0;">
            <table>
                <tr>
                    <td class="pad center bold lbl" style="width: 33%;">Prepared By</td>
                    <td class="pad center bold lbl" style="width: 33%;">Acknowledge By</td>
                    <td class="pad center bold lbl">Approved By</td>
                </tr>
                <tr>
                    <td class="sign"></td>
                    <td class="sign"></td>
                    <td class="sign"></td>
                </tr>
                <tr>
                    <td class="pad center">
                        <div style="border-top: 1px solid #1f3d5c; margin: 0 14px;"></div>
                    </td>
                    <td class="pad center">
                        <div style="border-top: 1px solid #1f3d5c; margin: 0 14px;"></div>
                    </td>
                    <td class="pad center">
                        <div style="border-top: 1px solid #1f3d5c; margin: 0 14px;"></div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
