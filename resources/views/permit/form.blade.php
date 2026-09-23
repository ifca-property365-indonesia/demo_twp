@extends($layout)

@php
    // $permit terisi saat membuka form dari History (ubah permit): bagian 1-2 hanya tampilan.
    // $locked = field bagian 3 yang tidak boleh diubah portal ini (admin: selain Work Tools).
    $isEdit  = !empty($permit);
    $detail  = $detail ?? null;
    $lines   = $lines ?? [];
    $tools   = $tools ?? [];
    $permit  = $permit ?? null;
    // Jam Kerja: D = 10.00-22.00, N = 22.00-10.00, O = lain-lain (jam diisi bebas)
    $shift   = $isEdit && $permit->complain_type === 'W'
        ? \App\Http\Controllers\BasePermitController::workShiftOf($permit->start_time, $permit->end_time)
        : 'D';
    $locked  = $locked ?? [];
    $type    = $isEdit ? $permit->complain_type : '';
    $base    = url($portal . '/permit');
    $fmtDate = function ($v) { return $v ? date('Y-m-d', strtotime($v)) : ''; };
    $fmtTime = function ($v) { return $v ? substr(trim($v), 0, 5) : ''; };

    // Atribut input bagian 3: terkunci -> hanya tampilan (tanpa name, tidak dikirim).
    $attr = function ($field, $label, $max = 50) use ($locked) {
        return in_array($field, $locked, true)
            ? 'readonly'
            : 'name="' . $field . '" maxlength="' . $max . '" required data-label="' . $label . '"';
    };
@endphp

@section('title', $isEdit ? 'Update Permit' : 'Request Permit')

@push('styles')
    <link rel="stylesheet" href="{{ url('assets/app/css/permit.css?ver=1.0.2') }}">
@endpush

@section('content')
    <div class="page-body">
        <div class="page-head permit-head">
            <div class="page-head-row">
                <div class="page-head-content">
                    <h3 class="page-title">{{ $isEdit ? 'Update Permit' : 'Request Permit' }}</h3>
                    <div class="page-desc text-body-secondary">
                        <p>
                            @if ($isEdit)
                                Permit type, number, tenant and unit cannot be changed.
                            @else
                                Work Permit, Entry Permit of Goods, or Exit Permit of Goods.
                            @endif
                        </p>
                    </div>
                </div>
                <div class="page-head-content">
                    <a href="{{ $base . '/history' }}" class="btn btn-outline-secondary d-none d-sm-inline-flex">
                        <i class="cil-history"></i><span>Permit History</span>
                    </a>
                    <a href="{{ $base . '/history' }}" class="btn btn-icon btn-outline-secondary d-inline-flex d-sm-none">
                        <i class="cil-history"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="page-block">
            <div class="card permit-card">
                <div class="card-body">
                    <form id="frmPermit" class="permit-form" method="POST" action="{{ $base . ($isEdit ? '/update' : '/save') }}" novalidate autocomplete="off">
                        @csrf
                        @if ($isEdit)<input type="hidden" name="doc_no" value="{{ $permit->complain_no }}">@endif

                        {{-- 1. Permit information --}}
                        <section class="permit-section">
                            <div class="permit-section__head">
                                <span class="permit-section__num">1</span>
                                <h6 class="permit-section__title" id="permitTitle">Permit Information</h6>
                                <span class="permit-no" title="{{ $isEdit ? 'Permit number' : 'Next permit number' }}">
                                    <i class="cil-clipboard"></i>
                                    <span id="permitNoBadge">{{ $letter_no !== '' ? $letter_no : '-' }}</span>
                                </span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="permit_type">Permit Type <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            @if ($isEdit)
                                                <input type="hidden" name="permit_type" id="permit_type" value="{{ $type }}">
                                                <input type="text" class="form-control" value="{{ $types[$type] ?? $type }}" readonly>
                                            @else
                                                <select name="permit_type" id="permit_type" class="form-control js-select2" required data-label="Permit Type" data-placeholder="Choose a permit type">
                                                    <option value=""></option>
                                                    @foreach ($types as $code => $label)
                                                        <option value="{{ $code }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="permit_no">Permit Number</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="permit_no" name="permit_no" value="{{ $letter_no }}" readonly>
                                        </div>
                                        @unless ($isEdit)<div class="form-note">Generated automatically when the permit is submitted.</div>@endunless
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="pemohon">Applicant</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="pemohon" name="pemohon" value="{{ $isEdit ? ($detail->member_name ?? $permit->serv_req_by) : ($applicant['name'] ?? '') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="handphone">Phone Number</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="handphone" name="handphone" value="{{ $isEdit ? ($detail->member_hp ?? $permit->contact_no) : ($applicant['hp'] ?? '') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- Placeholder sebelum jenis permit dipilih --}}
                        @unless ($isEdit)
                        <div class="alert alert-info d-flex align-items-center gap-2" id="permitEmpty">
                            <i class="cil-info fs-5"></i><div>
                            Select a <strong>Permit Type</strong> above to fill in the permit details.</div>
                        </div>
                        @endunless

                        {{-- 2. Location --}}
                        <section class="permit-section" id="sectionLocation" @unless($isEdit) hidden @endunless>
                            <div class="permit-section__head">
                                <span class="permit-section__num">2</span>
                                <h6 class="permit-section__title">Location</h6>
                                <span class="permit-section__hint">Tenant &rarr; Unit &rarr; Floor is filled automatically</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label class="form-label" for="tenant_no">Tenant <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            @if ($isEdit)
                                                @php
                                                    $tenancy = $tenancies->firstWhere('tenant_no', $permit->debtor_acct);
                                                    $tenantLabel = trim($permit->debtor_acct) . ($tenancy && $tenancy->entity_desc ? ' - ' . $tenancy->entity_desc : '');
                                                @endphp
                                                <input type="text" id="tenant_no" class="form-control" value="{{ $tenantLabel }}" readonly>
                                            @else
                                                <select name="tenant_no" id="tenant_no" class="form-control js-select2" required data-label="Tenant" data-placeholder="Choose a tenant">
                                                    <option value=""></option>
                                                    @foreach ($tenancies as $t)
                                                        <option value="{{ $t->id }}">{{ $t->tenant_no }}{{ $t->entity_desc ? ' - ' . $t->entity_desc : '' }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" for="lot_no">Unit <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            @if ($isEdit)
                                                <input type="text" id="lot_no" class="form-control" value="{{ $detail->unit ?? $permit->lot_no }}" readonly>
                                            @else
                                                <select name="lot_no" id="lot_no" class="form-control js-select2" required data-label="Unit" data-placeholder="Choose a unit">
                                                    <option value=""></option>
                                                </select>
                                            @endif
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label" for="floor">Floor <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="floor" {{ $isEdit ? '' : 'name=floor required' }} data-label="Floor" readonly placeholder="-" value="{{ $isEdit ? ($detail->floor ?? $permit->floor) : '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- 3a. Work Permit detail --}}
                        <section class="permit-section" id="sectionWork" hidden>
                            <div class="permit-section__head">
                                <span class="permit-section__num">3</span>
                                <h6 class="permit-section__title">Work Detail</h6>
                                @if ($locked)<span class="permit-section__hint">This section cannot be changed</span>@endif
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="contractor">Contractor Name <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="contractor" {!! $attr('contractor', 'Contractor Name') !!} value="{{ $detail->kontraktor_name ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="job_type">Job Type <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="job_type" {!! $attr('job_type', 'Job Type') !!} placeholder="e.g. Interior renovation" value="{{ $detail->work_type ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="incharge">Person in Charge <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="incharge" {!! $attr('incharge', 'Person in Charge') !!} value="{{ $detail->pic_name ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="pic_hp">Office Phone / HP <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="pic_hp" inputmode="tel" {!! $attr('pic_hp', 'Office Phone / HP', 20) !!} placeholder="Person in charge phone" value="{{ $detail->pic_hp ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- 3b. Permit of Goods detail --}}
                        <section class="permit-section" id="sectionGoods" hidden>
                            <div class="permit-section__head">
                                <span class="permit-section__num">3</span>
                                <h6 class="permit-section__title">Goods Detail</h6>
                                @if ($locked)<span class="permit-section__hint">This section cannot be changed</span>@endif
                            </div>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label class="form-label" for="company">Company Name <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="company" {!! $attr('company', 'Company Name') !!} value="{{ $detail->company_name ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" for="owner">Owner Name <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="owner" {!! $attr('owner', 'Owner Name') !!} value="{{ $detail->owner_name ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label" for="vehicle_no">Vehicle Number <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control text-uppercase" id="vehicle_no" {!! $attr('vehicle_no', 'Vehicle Number', 10) !!} placeholder="B 1234 XYZ" value="{{ $detail->vehicle_no ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- 4. Schedule & note --}}
                        <section class="permit-section" id="sectionSchedule" hidden>
                            <div class="permit-section__head">
                                <span class="permit-section__num">4</span>
                                <h6 class="permit-section__title">Schedule &amp; Notes</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label" for="start_date">Start Date <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control" id="start_date" name="start_date" required data-label="Start Date" value="{{ $fmtDate($permit->start_date ?? null) }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label" for="end_date">End Date <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control" id="end_date" name="end_date" required data-label="End Date" value="{{ $fmtDate($permit->end_date ?? null) }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 time-field" hidden>
                                    <div class="mb-2">
                                        <label class="form-label d-block">Working Hours <span class="req">*</span></label>
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach ($work_shifts as $code => $range)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="work_shift" id="work_shift_{{ $code }}" value="{{ $code }}"
                                                        data-start="{{ $range[0] }}" data-end="{{ $range[1] }}" @checked($shift === $code)>
                                                    <label class="form-check-label" for="work_shift_{{ $code }}">{{ $range[0] }} - {{ $range[1] }}</label>
                                                </div>
                                            @endforeach
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="work_shift" id="work_shift_O" value="O" @checked($shift === 'O')>
                                                <label class="form-check-label" for="work_shift_O">Other</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 time-field" hidden>
                                    <div class="mb-3">
                                        <label class="form-label" for="start_time">Start Time <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="time" class="form-control" id="start_time" name="start_time" required data-label="Start Time" value="{{ $fmtTime($permit->start_time ?? null) }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 time-field" hidden>
                                    <div class="mb-3">
                                        <label class="form-label" for="end_time">End Time <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="time" class="form-control" id="end_time" name="end_time" required data-label="End Time" value="{{ $fmtTime($permit->end_time ?? null) }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 time-field" hidden>
                                    <div class="form-note text-danger mt-0 mb-3">
                                        <i class="cil-info"></i>
                                        Start Time and End Time are the <strong>daily working hours</strong> for this permit,
                                        applied to every day between the start and end date &mdash; not the total duration of the work.
                                        End Time may be past midnight (e.g. 22:00 - 10:00).
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="note">Note <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control" id="note" name="note" rows="3" maxlength="500" required data-label="Note" placeholder="Describe the work / goods in detail">{{ $permit->note ?? '' }}</textarea>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="form-note"><span id="noteCount">0</span>/500</div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- 5. Workers / Items --}}
                        <section class="permit-section" id="sectionLines" hidden>
                            <div class="permit-section__head">
                                <span class="permit-section__num">5</span>
                                <h6 class="permit-section__title"><span id="linesTitle">Workers</span> <span class="req">*</span></h6>
                                <span class="permit-section__hint">Total <strong><span id="lineCount">0</span></strong> <span id="lineUnit">worker(s)</span> &middot; press Enter to add a new row</span>
                            </div>
                            <div class="permit-lines">
                                <div class="table-responsive">
                                    <table class="table" id="tblLines">
                                        <thead>
                                            <tr>
                                                <th class="line-no">No.</th>
                                                <th id="linesCol">Worker Name</th>
                                                <th class="line-act"></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="permit-lines__foot">
                                    <button type="button" class="btn btn-sm btn-primary" id="btnAddLine">
                                        <i class="cil-plus"></i><span>Add Worker</span>
                                    </button>
                                </div>
                            </div>
                        </section>

                        {{-- 6. Kegiatan & peralatan (Work Permit) --}}
                        <section class="permit-section" id="sectionTools" hidden>
                            <div class="permit-section__head">
                                <span class="permit-section__num">6</span>
                                <h6 class="permit-section__title">Work Activities &amp; Tools <span class="req">*</span></h6>
                                <span class="permit-section__hint"><span id="toolCount">0</span> row(s)</span>
                            </div>
                            <div class="permit-lines">
                                <div class="table-responsive">
                                    <table class="table" id="tblTools">
                                        <thead>
                                            <tr>
                                                <th class="line-no">No.</th>
                                                <th style="min-width: 14rem;">Job Type / Activity</th>
                                                <th style="min-width: 12rem;">Tools / PPE</th>
                                                <th style="min-width: 10rem;">Remarks</th>
                                                <th class="line-act"></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="permit-lines__foot">
                                    <button type="button" class="btn btn-sm btn-primary" id="btnAddTool">
                                        <i class="cil-plus"></i><span>Add Activity</span>
                                    </button>
                                </div>
                            </div>
                        </section>

                        <div class="permit-actions">
                            @if ($is_admin && $isEdit)
                                <div class="permit-status">
                                    <label class="form-label mb-1" for="set_status">Status after saving</label>
                                    <select name="set_status" id="set_status" class="form-select">
                                        <option value="">Modify</option>
                                        @foreach ($statuses as $code => $label)
                                            <option value="{{ $code }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <span class="hint">
                                <span class="d-block"><span class="req">*</span> Required fields</span>
                                <div class="form-note text-danger mt-0 mb-3">
                                        <span class="d-block mt-1"><i class="cil-info"></i>
                                        Operating hours are <strong>{{ $office_hours }}</strong> on working days.
                                        Requests submitted outside these hours will be processed on the next working day.
                                        </span>
                                    </div>
                            </span>
                            <button type="button" class="btn btn-outline-secondary" id="btnReset">
                                <i class="cil-reload"></i><span>Reset</span>
                            </button>
                            <button type="submit" class="btn btn-primary" id="btnSave" @unless($isEdit) disabled @endunless>
                                <i class="cil-send"></i><span>{{ $isEdit ? 'Save Changes' : 'Submit' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
(function ($) {
    'use strict';

    var URLS = {
        lots:     "{{ $base }}/lots",
        letterNo: "{{ $base }}/letterNo",
        history:  "{{ $base }}/history"
    };
    var TYPES    = @json($types);
    var IS_EDIT  = {{ $isEdit ? 'true' : 'false' }};
    var EDIT_LINES = @json(array_values((array) $lines));
    var EDIT_TOOLS = @json(array_values((array) $tools));
    var MIN_SPINNER_MS = 800;   // overlay tampil minimal segini supaya tidak berkedip

    var $form     = $('#frmPermit');
    var $type     = $('#permit_type');
    var $tenant   = $('#tenant_no');
    var $lot      = $('#lot_no');
    var $floor    = $('#floor');
    var $permitNo = $('#permit_no');
    var $tbody    = $('#tblLines tbody');
    var $tools    = $('#tblTools tbody');
    var $btnSave  = $('#btnSave');
    var $spinner  = $('#overlaySpinner');

    var sections = {
        location: $('#sectionLocation'),
        work:     $('#sectionWork'),
        goods:    $('#sectionGoods'),
        schedule: $('#sectionSchedule'),
        lines:    $('#sectionLines'),
        tools:    $('#sectionTools')
    };

    // ---------------------------------------------------------------
    // Select2
    // ---------------------------------------------------------------
    $('.js-select2').each(function () {
        $(this).select2({ width: '100%', placeholder: $(this).data('placeholder') || '' });
    });

    // ---------------------------------------------------------------
    // Tampilkan / sembunyikan bagian form sesuai jenis permit.
    // Input di bagian yang tersembunyi di-disable supaya tidak ikut terkirim.
    // ---------------------------------------------------------------
    function setVisible($el, show) {
        $el.prop('hidden', !show).find(':input').prop('disabled', !show);
    }

    function applyType() {
        var type   = $type.val();
        var chosen = !!TYPES[type];
        var isWork = type === 'W';

        $('#permitTitle').text(chosen ? TYPES[type] : 'Permit Information');
        $('#permitEmpty').prop('hidden', chosen);

        setVisible(sections.location, chosen);
        setVisible(sections.schedule, chosen);
        setVisible(sections.lines,    chosen);
        setVisible(sections.work,     isWork);
        setVisible(sections.tools,    isWork);
        setVisible(sections.goods,    chosen && !isWork);
        setVisible($('.time-field'),  isWork);

        $('#linesTitle').text(isWork ? 'Workers' : 'Items');
        $('#linesCol').text(isWork ? 'Worker Name' : 'Item Name');
        $('#lineUnit').text(isWork ? 'worker(s)' : 'item(s)');
        $('#btnAddLine span').text(isWork ? 'Add Worker' : 'Add Item');
        $tbody.find('input').attr('placeholder', linePlaceholder());

        // setVisible() meng-enable semua input; kembalikan status tombol hapus & jam kerja
        renumber();
        renumberTools();
        applyShift();

        if (IS_EDIT) {
            $btnSave.prop('disabled', false).find('span').text('Save Changes');
        } else {
            $btnSave.prop('disabled', !chosen)
                .find('span').text(chosen ? 'Submit ' + TYPES[type] : 'Submit');
        }

        clearErrors();
    }

    $type.on('change', applyType);

    // ---------------------------------------------------------------
    // Tenant -> Unit -> Floor (+ nomor permit mengikuti entity/project tenancy)
    // ---------------------------------------------------------------
    if (!IS_EDIT) {
    $tenant.on('change', function () {
        var id = $(this).val();

        $lot.empty().append('<option value=""></option>').val('').trigger('change.select2');
        $floor.val('');

        if (!id) {
            return;
        }

        $.get(URLS.lots + '/' + id)
            .done(function (html) {
                $lot.html(html).val('').trigger('change.select2');
                // Satu unit saja: langsung dipilih
                var $opts = $lot.find('option[value!=""]');
                if ($opts.length === 1) {
                    $lot.val($opts.val()).trigger('change');
                }
            })
            .fail(function () {
                toast('error', 'Failed to load units, please try again.');
            });

        $.getJSON(URLS.letterNo + '/' + id)
            .done(function (res) {
                if (res && res.letter_no) {
                    $permitNo.val(res.letter_no);
                    $('#permitNoBadge').text(res.letter_no);
                }
            });
    });

    $lot.on('change', function () {
        var level = $(this).find(':selected').data('level');
        $floor.val(level === undefined || level === null ? '' : level);
        if ($floor.val() !== '') {
            clearError($floor);
        }
    });
    }

    // Hanya satu tenancy: langsung dipilih
    function autoSelectTenant() {
        var $opts = $tenant.find('option[value!=""]');
        if ($opts.length === 1) {
            $tenant.val($opts.val()).trigger('change');
        }
    }

    if (!IS_EDIT) { autoSelectTenant(); }

    // ---------------------------------------------------------------
    // Tanggal: end >= start, default end = start
    // ---------------------------------------------------------------
    $('#start_date').on('change', function () {
        var v = $(this).val();
        $('#end_date').attr('min', v);
        if (v && (!$('#end_date').val() || $('#end_date').val() < v)) {
            $('#end_date').val(v);
        }
    });

    // ---------------------------------------------------------------
    // Jam Kerja: 10:00-22:00 / 22:00-10:00 mengisi jam otomatis, Other = isi sendiri
    // ---------------------------------------------------------------
    function applyShift() {
        var $opt = $('input[name="work_shift"]:checked');
        var fixed = $opt.length && $opt.val() !== 'O';

        if (fixed) {
            $('#start_time').val($opt.data('start'));
            $('#end_time').val($opt.data('end'));
            clearError($('#start_time'));
            clearError($('#end_time'));
        }
        $('#start_time, #end_time').prop('readonly', fixed);
    }

    $('input[name="work_shift"]').on('change', function () {
        if ($(this).val() === 'O') {
            $('#start_time, #end_time').val('');
        }
        applyShift();
        if ($(this).val() === 'O') {
            $('#start_time').trigger('focus');
        }
    });

    $('#note').on('input', function () {
        $('#noteCount').text($(this).val().length);
    });

    $('#vehicle_no').on('input', function () {
        this.value = this.value.toUpperCase();
    });

    // ---------------------------------------------------------------
    // Baris pekerja / barang
    // ---------------------------------------------------------------
    function linePlaceholder() {
        return $type.val() === 'W' ? 'Worker name' : 'Item name';
    }

    function addLine(focus) {
        var $tr = $(
            '<tr>' +
                '<td class="line-no"></td>' +
                '<td><div class="form-control-wrap">' +
                    '<input type="text" class="form-control" name="line_name[]" maxlength="50" autocomplete="off">' +
                    '<div class="invalid-feedback"></div>' +
                '</div></td>' +
                '<td class="line-act">' +
                    '<button type="button" class="btn btn-del" title="Remove row"><i class="cil-trash"></i></button>' +
                '</td>' +
            '</tr>'
        );

        $tr.find('input').attr('placeholder', linePlaceholder());
        $tbody.append($tr);
        renumber();

        if (focus) {
            $tr.find('input').trigger('focus');
        }
    }

    function renumber() {
        var $rows = $tbody.children('tr');
        $rows.each(function (i) {
            $(this).find('.line-no').text(i + 1);
        });
        $rows.find('.btn-del').prop('disabled', $rows.length <= 1);
        $('#lineCount').text($rows.length);
    }

    $('#btnAddLine').on('click', function () {
        addLine(true);
    });

    $tbody.on('click', '.btn-del', function () {
        if ($tbody.children('tr').length <= 1) {
            return;
        }
        $(this).closest('tr').remove();
        renumber();
    });

    // Enter di baris terakhir -> tambah baris baru
    $tbody.on('keydown', 'input', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if ($(this).closest('tr').is(':last-child')) {
                addLine(true);
            } else {
                $(this).closest('tr').next().find('input').trigger('focus');
            }
        }
    });

    if (IS_EDIT && EDIT_LINES.length) {
        EDIT_LINES.forEach(function (name) {
            addLine(false);
            $tbody.children('tr').last().find('input').val(name);
        });
    } else {
        addLine(false);
    }

    // ---------------------------------------------------------------
    // Baris kegiatan & peralatan (Work Permit)
    // ---------------------------------------------------------------
    function toolCell(name, max, placeholder) {
        return '<td><div class="form-control-wrap">' +
                '<input type="text" class="form-control" name="' + name + '[]" maxlength="' + max + '" placeholder="' + placeholder + '" autocomplete="off">' +
                '<div class="invalid-feedback"></div>' +
            '</div></td>';
    }

    function addTool(focus, data) {
        var $tr = $(
            '<tr>' +
                '<td class="line-no"></td>' +
                toolCell('tool_activity', 100, 'e.g. Ceiling installation') +
                toolCell('tool_name', 100, 'e.g. Ladder, helmet, gloves') +
                toolCell('tool_remarks', 255, 'Optional') +
                '<td class="line-act">' +
                    '<button type="button" class="btn btn-del" title="Remove row"><i class="cil-trash"></i></button>' +
                '</td>' +
            '</tr>'
        );

        if (data) {
            $tr.find('[name="tool_activity[]"]').val(data.activity || '');
            $tr.find('[name="tool_name[]"]').val(data.tool_name || '');
            $tr.find('[name="tool_remarks[]"]').val(data.remarks || '');
        }

        $tools.append($tr);
        renumberTools();

        if (focus) {
            $tr.find('input').first().trigger('focus');
        }
    }

    function renumberTools() {
        var $rows = $tools.children('tr');
        $rows.each(function (i) {
            $(this).find('.line-no').text(i + 1);
        });
        $rows.find('.btn-del').prop('disabled', $rows.length <= 1);
        $('#toolCount').text($rows.length);
    }

    $('#btnAddTool').on('click', function () {
        addTool(true);
    });

    $tools.on('click', '.btn-del', function () {
        if ($tools.children('tr').length <= 1) {
            return;
        }
        $(this).closest('tr').remove();
        renumberTools();
    });

    // Enter: pindah ke kolom berikutnya; di kolom terakhir baris terakhir -> baris baru
    $tools.on('keydown', 'input', function (e) {
        if (e.key !== 'Enter') {
            return;
        }
        e.preventDefault();
        var $inputs = $tools.find('input');
        var idx = $inputs.index(this);
        if (idx === $inputs.length - 1) {
            addTool(true);
        } else {
            $inputs.eq(idx + 1).trigger('focus');
        }
    });

    if (IS_EDIT && EDIT_TOOLS.length) {
        EDIT_TOOLS.forEach(function (t) {
            addTool(false, t);
        });
    } else {
        addTool(false);
    }

    // ---------------------------------------------------------------
    // Validasi inline
    // ---------------------------------------------------------------
    function feedbackOf($el) {
        return $el.closest('.form-control-wrap').find('.invalid-feedback').first();
    }

    function setError($el, msg) {
        $el.addClass('is-invalid');
        feedbackOf($el).text(msg).show();
    }

    function clearError($el) {
        $el.removeClass('is-invalid');
        feedbackOf($el).text('').hide();
    }

    function clearErrors() {
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('').hide();
    }

    $form.on('input change', ':input', function () {
        if ($(this).hasClass('is-invalid') && $.trim($(this).val()) !== '') {
            clearError($(this));
        }
    });

    function validate() {
        clearErrors();
        var $first = null;

        function fail($el, msg) {
            setError($el, msg);
            if (!$first) {
                $first = $el;
            }
        }

        $form.find(':input[required]:enabled').each(function () {
            if ($.trim($(this).val()) === '') {
                fail($(this), ($(this).data('label') || 'This field') + ' is required.');
            }
        });

        var start = $('#start_date').val(), end = $('#end_date').val();
        if (start && end && end < start) {
            fail($('#end_date'), 'End Date cannot be earlier than Start Date.');
        }

        if ($type.val() === 'W') {
            var st = $('#start_time').val(), et = $('#end_time').val();
            // boleh lewat tengah malam (22:00 - 10:00), asal tidak sama
            if (st && et && et === st) {
                fail($('#end_time'), 'End Time must be different from Start Time.');
            }
        }

        $tbody.find('input:enabled').each(function () {
            if ($.trim($(this).val()) === '') {
                fail($(this), linePlaceholder() + ' cannot be empty.');
            }
        });

        $tools.find('[name="tool_activity[]"]:enabled, [name="tool_name[]"]:enabled').each(function () {
            if ($.trim($(this).val()) === '') {
                fail($(this), (this.name === 'tool_name[]' ? 'Tools / PPE' : 'Activity') + ' cannot be empty.');
            }
        });

        if ($first) {
            scrollTo($first);
            toast('error', 'Please complete the highlighted fields.');
            return false;
        }

        return true;
    }

    // Pesan validasi dari server ({field: [msg]}, field baris: worker_name.0 / item_name.0)
    function showServerErrors(errors) {
        var $first = null;

        $.each(errors, function (key, msgs) {
            var msg = $.isArray(msgs) ? msgs[0] : msgs;
            var m = /^(worker_name|item_name)\.(\d+)$/.exec(key);
            var t = /^(tool_activity|tool_name|tool_remarks)\.(\d+)$/.exec(key);
            var $el;

            if (m) {
                $el = $tbody.children('tr').eq(parseInt(m[2], 10)).find('input');
            } else if (key === 'worker_name' || key === 'item_name') {
                $el = $tbody.find('input').first();
            } else if (t) {
                $el = $tools.children('tr').eq(parseInt(t[2], 10)).find('[name="' + t[1] + '[]"]');
            } else if (/^tool_/.test(key)) {
                $el = $tools.find('input').first();
            } else if (key === 'work_shift') {
                $el = $('#start_time');
            } else {
                $el = $form.find('[name="' + key + '"]');
            }

            if ($el && $el.length) {
                setError($el, msg);
                if (!$first) {
                    $first = $el;
                }
            }
        });

        if ($first) {
            scrollTo($first);
        }
    }

    function scrollTo($el) {
        var $target = $el.is('select') ? $el.next('.select2-container') : $el;
        $('html, body').animate({ scrollTop: Math.max(0, $target.offset().top - 120) }, 250);
        if (!$el.is('select')) {
            $el.trigger('focus');
        }
    }

    // ---------------------------------------------------------------
    // Submit
    // ---------------------------------------------------------------
    function payload() {
        var lineKey = $type.val() === 'W' ? 'worker_name[]' : 'item_name[]';
        return $.map($form.serializeArray(), function (f) {
            if (f.name === 'line_name[]') {
                f.name = lineKey;
            }
            return f;
        });
    }

    $form.on('submit', function (e) {
        e.preventDefault();

        if (!TYPES[$type.val()]) {
            setError($type, 'Permit Type is required.');
            return;
        }

        if (!validate()) {
            return;
        }

        Swal.fire({
            title: (IS_EDIT ? 'Save changes to ' : 'Submit ') + TYPES[$type.val()] + '?',
            html: 'Permit number <strong>' + escapeHtml($permitNo.val() || '-') + '</strong> will be ' + (IS_EDIT ? 'updated.' : 'submitted for approval.'),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: IS_EDIT ? 'Yes, save' : 'Yes, submit',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(function (result) {
            if (result.value) {
                send();
            }
        });
    });

    function send() {
        var started = Date.now();

        $btnSave.prop('disabled', true);
        $('#overlaySpinnerText').text('Submitting permit, please wait...');
        $spinner.css('display', 'flex');

        function finish(cb) {
            setTimeout(function () {
                $spinner.hide();
                cb();
            }, Math.max(0, MIN_SPINNER_MS - (Date.now() - started)));
        }

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: payload(),
            dataType: 'json'
        }).done(function (res) {
            finish(function () {
                if (res.status === 'OK') {
                    Swal.fire({
                        title: IS_EDIT ? 'Permit Updated' : 'Permit Submitted',
                        html: escapeHtml(res.pesan) + '<br><small class="text-body-secondary">You will be redirected to Permit History.</small>',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    }).then(function () {
                        window.location.href = URLS.history;
                    });
                } else {
                    $btnSave.prop('disabled', false);
                    Swal.fire({ title: 'Failed', text: res.pesan, icon: 'error' });
                }
            });
        }).fail(function (xhr, textStatus, errorThrown) {
            finish(function () {
                $btnSave.prop('disabled', false);
                var res = xhr.responseJSON || {};

                if (res.errors) {
                    showServerErrors(res.errors);
                }

                Swal.fire({
                    title: xhr.status === 422 ? 'Please check the form' : 'Error',
                    text: res.pesan || (textStatus + ': ' + errorThrown),
                    icon: 'error'
                });
            });
        });
    }

    // ---------------------------------------------------------------
    // Reset
    // ---------------------------------------------------------------
    $('#btnReset').on('click', function () {
        if (IS_EDIT) {
            // kembalikan ke data tersimpan
            window.location.reload();
            return;
        }
        Swal.fire({
            title: 'Reset the form?',
            text: 'All entered data will be cleared.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, reset',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(function (result) {
            if (!result.value) {
                return;
            }
            $form[0].reset();
            $form.find('.js-select2').val('').trigger('change');
            $tbody.empty();
            addLine(false);
            $tools.empty();
            addTool(false);
            $('#work_shift_D').prop('checked', true);
            $('#noteCount').text('0');
            $('#end_date').removeAttr('min');
            applyType();
            autoSelectTenant();
            $('html, body').animate({ scrollTop: 0 }, 250);
        });
    });

    // ---------------------------------------------------------------
    // Util
    // ---------------------------------------------------------------
    function toast(icon, text) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: text,
            showConfirmButton: false,
            timer: 2500
        });
    }

    applyType();
})(jQuery);
</script>
@endpush
