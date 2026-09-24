@extends($layout)

@php $base = url($portal . '/permit'); @endphp

@section('title', __('shared/permit.permit_history'))

@push('styles')
    <link rel="stylesheet" href="{{ url('assets/app/css/permit.css?ver=1.0.2') }}">
@endpush

@section('content')
    <div class="page-body">
        <div class="page-head permit-head">
            <div class="page-head-row">
                <div class="page-head-content">
                    <h3 class="page-title">{{ __('shared/permit.permit_history') }}</h3>
                    <div class="page-desc text-body-secondary">
                        <p>{{ __('shared/permit.history_desc') }}</p>
                    </div>
                </div>
                <div class="page-head-content">
                    <a href="{{ $base . '/add' }}" class="btn btn-primary d-none d-sm-inline-flex">
                        <i class="cil-plus"></i><span>{{ __('shared/permit.request_permit') }}</span>
                    </a>
                    <a href="{{ $base . '/add' }}" class="btn btn-icon btn-primary d-inline-flex d-sm-none">
                        <i class="cil-plus"></i>
                    </a>
                </div>
            </div>
        </div>

        @if (session('alert'))
            <div class="alert alert-warning d-flex align-items-center gap-2"><i class="cil-warning"></i><div>{{ session('alert') }}</div></div>
        @endif

        <div class="page-block">
            {{-- Filter --}}
            <div class="card permit-card mb-3">
                <div class="card-body">
                    <form id="formSearch" class="permit-filter" method="GET" action="" novalidate autocomplete="off">
                        <div class="row g-3 align-items-end">
                            <div class="col-sm-6 col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label" for="permit_no">{{ __('shared/permit.permit_no') }}</label>
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-left"><i class="cil-search"></i></div>
                                        <input type="text" id="permit_no" name="permit_no" class="form-control" placeholder="{{ __('shared/permit.ph_all_permit_no') }}">
                                    </div>
                                </div>
                            </div>
                            @if ($is_admin)
                            <div class="col-sm-6 col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label" for="tenant_no">{{ __('common.tenant') }}</label>
                                    <div class="form-control-wrap">
                                        <select id="tenant_no" name="tenant_no" class="form-select">
                                            <option value="">{{ __('shared/permit.all_tenants') }}</option>
                                            @foreach ($tenants as $t)
                                                <option value="{{ $t->tenant_no }}">{{ $t->tenant_no }}{{ $t->entity_desc ? ' - ' . $t->entity_desc : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-sm-6 col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label" for="permit_type">{{ __('shared/permit.permit_type') }}</label>
                                    <div class="form-control-wrap">
                                        <select id="permit_type" name="permit_type" class="form-select">
                                            <option value="">{{ __('shared/permit.all_types') }}</option>
                                            @foreach ($types as $code => $label)
                                                <option value="{{ $code }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-2">
                                <div class="mb-3">
                                    <label class="form-label" for="status">{{ __('common.status') }}</label>
                                    <div class="form-control-wrap">
                                        <select id="status" name="status" class="form-select">
                                            <option value="">{{ __('shared/permit.all_status') }}</option>
                                            @foreach ($statuses as $code => $label)
                                                <option value="{{ $code }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-2">
                                <div class="mb-3">
                                    <label class="form-label" for="start_date">{{ __('common.start_date') }}</label>
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                        <input type="text" id="start_date" name="start_date" class="form-control date-picker" data-date-format="dd/mm/yyyy" placeholder="{{ __('shared/permit.all_dates') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="mb-3 d-flex">
                                    <button type="submit" class="btn btn-primary me-1 flex-grow-1" id="btnSearch">
                                        <i class="cil-filter"></i><span>{{ __('common.filter') }}</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="btnReset" title="{{ __('shared/permit.reset_filter') }}">
                                        <i class="cil-reload"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="card permit-card">
                <div class="card-body permit-table-wrap">
                    <div class="table-responsive">
                        <table id="tblPermit" class="table table-bordered table-hover permit-table w-100">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 48px;">{{ __('common.col_no') }}</th>
                                    <th>{{ __('shared/permit.permit_no') }}</th>
                                    @if ($is_admin)<th>{{ __('common.tenant') }}</th>@endif
                                    <th>{{ __('common.type') }}</th>
                                    <th class="text-center">{{ __('common.tower') }}</th>
                                    <th class="text-center">{{ __('common.floor') }}</th>
                                    <th class="text-center">{{ __('common.unit') }}</th>
                                    <th>{{ __('common.description') }}</th>
                                    <th>{{ __('shared/permit.col_start') }}</th>
                                    <th>{{ __('shared/permit.col_end') }}</th>
                                    <th class="text-center">{{ __('common.time') }}</th>
                                    <th class="text-center">{{ __('common.status') }}</th>
                                    <th class="text-center" style="width: 132px;">{{ __('common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function ($) {
    'use strict';

    var TYPES    = @json($types);
    var STATUSES = @json($statuses);
    // Teks UI sesuai bahasa aktif; placeholder :no diganti lewat t()
    @php
        $jsLang = [
            'update_no'     => __('shared/permit.update_no'),
            'cancel_no'     => __('shared/permit.cancel_no'),
            'print_no'      => __('shared/permit.print_no'),
            'only_approved' => __('shared/permit.only_approved'),
            'cancel_title'  => __('shared/permit.cancel_title'),
            'cancel_text'   => __('shared/permit.cancel_text'),
            'yes_cancel'    => __('shared/permit.yes_cancel'),
            'no'            => __('common.no'),
            'information'   => __('common.information'),
            'error'         => __('common.error'),
            'load_failed'   => __('shared/permit.load_failed'),
        ];
    @endphp
    var LANG = @json($jsLang);
    var PRINT_URL  = "{{ $base }}/print";
    var EDIT_URL   = "{{ $base }}/edit";
    var CANCEL_URL = "{{ $base }}/cancel";
    var EDITABLE  = @json(array_values($editable));
    var PRINTABLE = @json(\App\Http\Controllers\BasePermitController::PRINTABLE_STATUS);

    var TYPE_BADGE   = { W: 'badge-soft-primary', I: 'badge-soft-success', O: 'badge-soft-warning' };
    var STATUS_BADGE = {
        R: 'badge-soft-info',     A: 'badge-soft-primary', S: 'badge-soft-secondary',
        P: 'badge-soft-warning',  F: 'badge-soft-primary', M: 'badge-soft-secondary',
        Z: 'badge-soft-success',  Y: 'badge-soft-success', C: 'badge-soft-dark',
        X: 'badge-soft-danger'
    };

    function t(text, params) {
        $.each(params || {}, function (key, value) {
            text = text.split(':' + key).join(value);
        });
        return text;
    }

    function esc(s) {
        return $('<div>').text(s == null ? '' : String(s)).html();
    }

    function dash(v) {
        return (v == null || String(v).trim() === '') ? '-' : esc(v);
    }

    function fmtDate(v) {
        return (v == null || v === '') ? '-' : moment(v).format('DD MMM YYYY');
    }

    // start_time/end_time varchar(5) "HH:MM"; dipotong kalau suatu saat berisi detik
    function fmtTime(v) {
        return (v == null || v === '') ? '' : String(v).substring(0, 5);
    }

    function badge(cls, text) {
        return '<span class="badge ' + cls + '">' + esc(text) + '</span>';
    }

    var table = $('#tblPermit').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [],              // urutan default (start_date lalu permit no, terbaru dulu) diatur di server
        autoWidth: false,
        language: {
            search: '',
            searchPlaceholder: @json(__('shared/permit.dt_search')),
            processing: @json(__('common.loading')),
            emptyTable: @json(__('shared/permit.dt_empty')),
            zeroRecords: @json(__('shared/permit.dt_zero')),
            info: @json(__('shared/permit.dt_info')),
            infoEmpty: @json(__('shared/permit.dt_info_empty')),
            infoFiltered: @json(__('shared/permit.dt_filtered')),
            lengthMenu: @json(__('shared/permit.dt_length'))
        },
        ajax: {
            url: "{{ $base }}/historyTable",
            data: function (d) {
                d.permit_no   = $('#permit_no').val();
                d.tenant_no   = $('#tenant_no').val();
                d.permit_type = $('#permit_type').val();
                d.status      = $('#status').val();
                d.start_date  = $('#start_date').val();
            },
            error: function (xhr) {
                if (xhr.status === 401 || xhr.status === 419) {
                    window.location.reload();
                    return;
                }
                Swal.fire({ title: LANG.error, text: LANG.load_failed, icon: 'error' });
            }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'complain_no', className: 'nowrap',
                render: function (d) { return '<span class="permit-link">' + esc(d) + '</span>'; } },
            @if ($is_admin){ data: 'debtor_acct', className: 'nowrap', render: dash },@endif
            { data: 'complain_type', className: 'nowrap',
                render: function (d) { return TYPES[d] ? badge(TYPE_BADGE[d] || 'badge-soft-secondary', TYPES[d]) : dash(d); } },
            { data: 'tower', className: 'text-center', render: dash },
            { data: 'floor', className: 'text-center', render: dash },
            { data: 'unit',  className: 'text-center nowrap', render: dash },
            { data: 'note',  className: 'col-note', render: dash },
            { data: 'start_date', className: 'nowrap', render: fmtDate },
            { data: 'end_date',   className: 'nowrap', render: fmtDate },
            { data: 'start_time', className: 'text-center nowrap',
                render: function (d, type, row) {
                    var s = fmtTime(d), e = fmtTime(row.end_time);
                    if (!s && !e) { return '-'; }
                    return esc(s) + (e ? ' - ' + esc(e) : '');
                } },
            { data: 'status', className: 'text-center nowrap',
                render: function (d) {
                    var code = (d == null) ? '' : String(d).trim();
                    return STATUSES[code] ? badge(STATUS_BADGE[code] || 'badge-soft-secondary', STATUSES[code]) : dash(code);
                } },
            { data: 'complain_no', orderable: false, searchable: false, className: 'text-center nowrap',
                render: function (d, type, row) {
                    var html = '';
                    var status = (row.status == null) ? '' : String(row.status).trim();

                    if (EDITABLE.indexOf(status) >= 0) {
                        html += '<a href="' + EDIT_URL + '/' + encodeURIComponent(d) + '" ' +
                            'class="btn btn-sm btn-outline-secondary btn-print me-1" title="' + esc(t(LANG.update_no, { no: d })) + '">' +
                            '<i class="cil-pencil"></i></a>';
                        html += '<button type="button" class="btn btn-sm btn-outline-danger btn-print btn-cancel me-1" ' +
                            'data-permit="' + esc(d) + '" title="' + esc(t(LANG.cancel_no, { no: d })) + '">' +
                            '<i class="cil-ban"></i></button>';
                    }
                    // Hanya permit Approved yang bisa dicetak (dicek juga di server)
                    if (status !== PRINTABLE) {
                        html += '<span class="btn btn-sm btn-outline-primary btn-print disabled" ' +
                            'title="' + esc(LANG.only_approved) + '" aria-disabled="true">' +
                            '<i class="cil-print"></i></span>';
                    } else {
                        html += '<a href="' + PRINT_URL + '/' + encodeURIComponent(d) + '" target="_blank" rel="noopener" ' +
                            'class="btn btn-sm btn-outline-primary btn-print" title="' + esc(t(LANG.print_no, { no: d })) + '">' +
                            '<i class="cil-print"></i></a>';
                    }
                    return html;
                } }
        ]
    });

    $('#formSearch').on('submit', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#permit_type, #status, #tenant_no').on('change', function () {
        table.ajax.reload();
    });

    // Batalkan permit langsung dari tabel
    $('#tblPermit').on('click', '.btn-cancel', function () {
        var permitNo = $(this).data('permit');

        Swal.fire({
            title: t(LANG.cancel_title, { no: permitNo }),
            text: LANG.cancel_text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: LANG.yes_cancel,
            cancelButtonText: LANG.no,
            reverseButtons: true,
            confirmButtonColor: '#e55353'
        }).then(function (r) {
            if (!r.value) { return; }

            $.ajax({ url: CANCEL_URL, type: 'POST', data: { doc_no: permitNo }, dataType: 'json' })
                .done(function (res) {
                    Swal.fire({ title: LANG.information, icon: res.status === 'OK' ? 'success' : 'error', text: res.pesan });
                    table.ajax.reload(null, false);
                })
                .fail(function (xhr, textStatus, errorThrown) {
                    var res = xhr.responseJSON || {};
                    Swal.fire({ title: LANG.error, icon: 'error', text: res.pesan || (textStatus + ' : ' + errorThrown) });
                    table.ajax.reload(null, false);
                });
        });
    });

    $('#btnReset').on('click', function () {
        $('#permit_no, #permit_type, #status, #tenant_no').val('');
        $('#start_date').val('');
        if ($.fn.datepicker) {
            $('#start_date').datepicker('update', '');
        }
        table.ajax.reload();
    });
})(jQuery);
</script>
@endpush
