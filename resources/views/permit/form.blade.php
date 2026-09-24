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
    $fmtDate = function ($v) { return $v ? date('d/m/Y', strtotime($v)) : ''; };
    $fmtTime = function ($v) { return $v ? substr(trim($v), 0, 5) : ''; };

    // Atribut input bagian 3: terkunci -> hanya tampilan (tanpa name, tidak dikirim).
    $attr = function ($field, $label, $max = 50) use ($locked) {
        return in_array($field, $locked, true)
            ? 'readonly'
            : 'name="' . $field . '" maxlength="' . $max . '" required data-label="' . e($label) . '"';
    };
@endphp

@section('title', $isEdit ? __('shared/permit.update_permit') : __('shared/permit.request_permit'))

@push('styles')
    <link rel="stylesheet" href="{{ url('assets/app/css/permit.css?ver=1.0.2') }}">
@endpush

@section('content')
    <div class="page-body">
        <div class="page-head permit-head">
            <div class="page-head-row">
                <div class="page-head-content">
                    <h3 class="page-title">{{ $isEdit ? __('shared/permit.update_permit') : __('shared/permit.request_permit') }}</h3>
                    <div class="page-desc text-body-secondary">
                        <p>
                            @if ($isEdit)
                                {{ __('shared/permit.form_desc_edit') }}
                            @else
                                {{ __('shared/permit.form_desc_new') }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="page-head-content">
                    <a href="{{ $base . '/history' }}" class="btn btn-outline-secondary d-none d-sm-inline-flex">
                        <i class="cil-history"></i><span>{{ __('shared/permit.permit_history') }}</span>
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
                                <h6 class="permit-section__title" id="permitTitle">{{ __('shared/permit.section_info') }}</h6>
                                <span class="permit-no" title="{{ $isEdit ? __('shared/permit.permit_number') : __('shared/permit.next_permit_number') }}">
                                    <i class="cil-clipboard"></i>
                                    <span id="permitNoBadge">{{ $letter_no !== '' ? $letter_no : '-' }}</span>
                                </span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="permit_type">{{ __('shared/permit.permit_type') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            @if ($isEdit)
                                                <input type="hidden" name="permit_type" id="permit_type" value="{{ $type }}">
                                                <input type="text" class="form-control" value="{{ $types[$type] ?? $type }}" readonly>
                                            @else
                                                <select name="permit_type" id="permit_type" class="form-control js-select2" required data-label="{{ __('shared/permit.permit_type') }}" data-placeholder="{{ __('shared/permit.ph_choose_type') }}">
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
                                        <label class="form-label" for="permit_no">{{ __('shared/permit.permit_number') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="permit_no" name="permit_no" value="{{ $letter_no }}" readonly>
                                        </div>
                                        @unless ($isEdit)<div class="form-note">{{ __('shared/permit.permit_no_auto') }}</div>@endunless
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="pemohon">{{ __('shared/permit.applicant') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="pemohon" name="pemohon" value="{{ $isEdit ? ($detail->member_name ?? $permit->serv_req_by) : ($applicant['name'] ?? '') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="handphone">{{ __('shared/permit.phone_number') }}</label>
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
                            {!! __('shared/permit.select_type_first') !!}</div>
                        </div>
                        @endunless

                        {{-- 2. Location --}}
                        <section class="permit-section" id="sectionLocation" @unless($isEdit) hidden @endunless>
                            <div class="permit-section__head">
                                <span class="permit-section__num">2</span>
                                <h6 class="permit-section__title">{{ __('shared/permit.section_location') }}</h6>
                                <span class="permit-section__hint">{{ __('shared/permit.hint_location') }}</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label class="form-label" for="tenant_no">{{ __('common.tenant') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            @if ($isEdit)
                                                @php
                                                    $tenancy = $tenancies->firstWhere('tenant_no', $permit->debtor_acct);
                                                    $tenantLabel = trim($permit->debtor_acct) . ($tenancy && $tenancy->entity_desc ? ' - ' . $tenancy->entity_desc : '');
                                                @endphp
                                                <input type="text" id="tenant_no" class="form-control" value="{{ $tenantLabel }}" readonly>
                                            @else
                                                <select name="tenant_no" id="tenant_no" class="form-control js-select2" required data-label="{{ __('common.tenant') }}" data-placeholder="{{ __('shared/permit.ph_choose_tenant') }}">
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
                                        <label class="form-label" for="lot_no">{{ __('common.unit') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            @if ($isEdit)
                                                <input type="text" id="lot_no" class="form-control" value="{{ $detail->unit ?? $permit->lot_no }}" readonly>
                                            @else
                                                <select name="lot_no" id="lot_no" class="form-control js-select2" required data-label="{{ __('common.unit') }}" data-placeholder="{{ __('shared/permit.ph_choose_unit') }}">
                                                    <option value=""></option>
                                                </select>
                                            @endif
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label" for="floor">{{ __('common.floor') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="floor" {{ $isEdit ? '' : 'name=floor required' }} data-label="{{ __('common.floor') }}" readonly placeholder="-" value="{{ $isEdit ? ($detail->floor ?? $permit->floor) : '' }}">
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
                                <h6 class="permit-section__title">{{ __('shared/permit.section_work') }}</h6>
                                @if ($locked)<span class="permit-section__hint">{{ __('shared/permit.hint_locked') }}</span>@endif
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="contractor">{{ __('shared/permit.contractor_name') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="contractor" {!! $attr('contractor', __('shared/permit.contractor_name')) !!} value="{{ $detail->kontraktor_name ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="job_type">{{ __('shared/permit.job_type') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="job_type" {!! $attr('job_type', __('shared/permit.job_type')) !!} placeholder="{{ __('shared/permit.ph_job_type_work') }}" value="{{ $detail->work_type ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="incharge">{{ __('shared/permit.person_in_charge') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="incharge" {!! $attr('incharge', __('shared/permit.person_in_charge')) !!} value="{{ $detail->pic_name ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="pic_hp">{{ __('shared/permit.office_phone') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="pic_hp" inputmode="tel" {!! $attr('pic_hp', __('shared/permit.office_phone'), 20) !!} placeholder="{{ __('shared/permit.ph_pic_phone') }}" value="{{ $detail->pic_hp ?? '' }}">
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
                                <h6 class="permit-section__title">{{ __('shared/permit.section_goods') }}</h6>
                                @if ($locked)<span class="permit-section__hint">{{ __('shared/permit.hint_locked') }}</span>@endif
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="owner">{{ __('shared/permit.owner_name') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="owner" {!! $attr('owner', __('shared/permit.owner_name')) !!} value="{{ $detail->owner_name ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="goods_job_type">{{ __('shared/permit.job_type') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="goods_job_type" {!! $attr('job_type', __('shared/permit.job_type')) !!} placeholder="{{ __('shared/permit.ph_job_type_goods') }}" value="{{ $detail->work_type ?? '' }}">
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
                                <h6 class="permit-section__title">{{ __('shared/permit.section_schedule') }}</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label" for="start_date">{{ __('common.start_date') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                            <input type="text" class="form-control date-picker" data-date-format="dd/mm/yyyy" placeholder="{{ __('common.select_date') }}" autocomplete="off" id="start_date" name="start_date" required data-label="{{ __('common.start_date') }}" value="{{ $fmtDate($permit->start_date ?? null) }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label" for="end_date">{{ __('common.end_date') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                            <input type="text" class="form-control date-picker" data-date-format="dd/mm/yyyy" placeholder="{{ __('common.select_date') }}" autocomplete="off" id="end_date" name="end_date" required data-label="{{ __('common.end_date') }}" value="{{ $fmtDate($permit->end_date ?? null) }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 shift-field" hidden>
                                    <div class="mb-2">
                                        <label class="form-label d-block">{{ __('shared/permit.working_hours') }} <span class="req">*</span></label>
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
                                                <label class="form-check-label" for="work_shift_O">{{ __('shared/permit.other') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 time-field" hidden>
                                    <div class="mb-3">
                                        <label class="form-label" for="start_time">{{ __('common.start_time') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="time" class="form-control" id="start_time" name="start_time" required data-label="{{ __('common.start_time') }}" value="{{ $fmtTime($permit->start_time ?? null) }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 time-field" hidden>
                                    <div class="mb-3">
                                        <label class="form-label" for="end_time">{{ __('common.end_time') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="time" class="form-control" id="end_time" name="end_time" required data-label="{{ __('common.end_time') }}" value="{{ $fmtTime($permit->end_time ?? null) }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 shift-field" hidden>
                                    <div class="form-note text-danger mt-0 mb-3">
                                        <i class="cil-info"></i>
                                        {!! __('shared/permit.note_daily_hours') !!}
                                    </div>
                                </div>
                                <div class="col-12 goods-field" hidden>
                                    <div class="form-note text-danger mt-0 mb-3">
                                        <i class="cil-info"></i>
                                        {!! __('shared/permit.note_goods_hours') !!}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="note">{{ __('common.note') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control" id="note" name="note" rows="3" maxlength="500" required data-label="{{ __('common.note') }}" placeholder="{{ __('shared/permit.ph_note') }}">{{ $permit->note ?? '' }}</textarea>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="form-note"><span id="noteCount">0</span>/500</div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- 5a. Pekerja (Work Permit) --}}
                        <section class="permit-section" id="sectionLines" hidden>
                            <div class="permit-section__head">
                                <span class="permit-section__num">5</span>
                                <h6 class="permit-section__title">{{ __('shared/permit.section_workers') }} <span class="req">*</span></h6>
                                <span class="permit-section__hint">{!! __('shared/permit.hint_workers', ['count' => '<span id="lineCount">0</span>']) !!}</span>
                            </div>
                            <div class="permit-lines">
                                <div class="table-responsive">
                                    <table class="table" id="tblLines">
                                        <thead>
                                            <tr>
                                                <th class="line-no">{{ __('common.col_no') }}</th>
                                                <th>{{ __('shared/permit.worker_name') }}</th>
                                                <th class="line-act"></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="permit-lines__foot">
                                    <button type="button" class="btn btn-sm btn-primary" id="btnAddLine">
                                        <i class="cil-plus"></i><span>{{ __('shared/permit.add_worker') }}</span>
                                    </button>
                                </div>
                            </div>
                        </section>

                        {{-- 6. Kegiatan & peralatan (Work Permit) --}}
                        <section class="permit-section" id="sectionTools" hidden>
                            <div class="permit-section__head">
                                <span class="permit-section__num">6</span>
                                <h6 class="permit-section__title">{{ __('shared/permit.section_tools') }} <span class="req">*</span></h6>
                                <span class="permit-section__hint">{!! __('shared/permit.hint_rows', ['count' => '<span id="toolCount">0</span>']) !!}</span>
                            </div>
                            <div class="permit-lines">
                                <div class="table-responsive">
                                    <table class="table" id="tblTools">
                                        <thead>
                                            <tr>
                                                <th class="line-no">{{ __('common.col_no') }}</th>
                                                <th style="min-width: 14rem;">{{ __('shared/permit.activity_col') }}</th>
                                                <th style="min-width: 12rem;">{{ __('shared/permit.tools_ppe') }}</th>
                                                <th style="min-width: 10rem;">{{ __('common.remarks') }}</th>
                                                <th class="line-act"></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="permit-lines__foot">
                                    <button type="button" class="btn btn-sm btn-primary" id="btnAddTool">
                                        <i class="cil-plus"></i><span>{{ __('shared/permit.add_activity') }}</span>
                                    </button>
                                </div>
                            </div>
                        </section>

                        {{-- 5b. Daftar barang (Entry / Exit Permit of Goods) --}}
                        <section class="permit-section" id="sectionItems" hidden>
                            <div class="permit-section__head">
                                <span class="permit-section__num">5</span>
                                <h6 class="permit-section__title">{{ __('shared/permit.section_items') }} <span class="req">*</span></h6>
                                <span class="permit-section__hint">{!! __('shared/permit.hint_items', ['count' => '<span id="itemCount">0</span>']) !!}</span>
                            </div>
                            <div class="permit-lines">
                                <div class="table-responsive">
                                    <table class="table" id="tblItems">
                                        <thead>
                                            <tr>
                                                <th class="line-no">{{ __('common.col_no') }}</th>
                                                <th style="min-width: 14rem;">{{ __('shared/permit.type_of_goods') }}</th>
                                                <th style="min-width: 7rem; width: 9rem;">{{ __('shared/permit.quantity') }}</th>
                                                <th style="min-width: 10rem;">{{ __('common.remarks') }}</th>
                                                <th class="line-act"></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="permit-lines__foot">
                                    <button type="button" class="btn btn-sm btn-primary" id="btnAddItem">
                                        <i class="cil-plus"></i><span>{{ __('shared/permit.add_item') }}</span>
                                    </button>
                                </div>
                            </div>
                        </section>

                        {{-- 6. Pengirim / pengambil & kendaraan (Entry / Exit Permit of Goods) --}}
                        <section class="permit-section" id="sectionSender" hidden>
                            <div class="permit-section__head">
                                <span class="permit-section__num">6</span>
                                <h6 class="permit-section__title">{{ __('shared/permit.section_sender') }}</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="sender_name">{{ __('shared/permit.sender_name') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="sender_name" {!! $attr('sender_name', __('shared/permit.sender_name')) !!} value="{{ $detail->sender_name ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label" for="sender_id_no">{{ __('shared/permit.sender_id_no') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="sender_id_no" {!! $attr('sender_id_no', __('shared/permit.sender_id_no'), 30) !!} value="{{ $detail->sender_id_no ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label" for="sender_hp">{{ __('shared/permit.phone_number') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="sender_hp" inputmode="tel" {!! $attr('sender_hp', __('shared/permit.phone_number'), 20) !!} value="{{ $detail->sender_hp ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="sender_address">{{ __('shared/permit.address') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="sender_address" {!! $attr('sender_address', __('shared/permit.address'), 255) !!} value="{{ $detail->sender_address ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="vehicle_type">{{ __('shared/permit.vehicle_type') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="vehicle_type" {!! $attr('vehicle_type', __('shared/permit.vehicle_type'), 30) !!} placeholder="{{ __('shared/permit.ph_vehicle_type') }}" value="{{ $detail->vehicle_type ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="vehicle_no">{{ __('shared/permit.vehicle_number') }} <span class="req">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control text-uppercase" id="vehicle_no" {!! $attr('vehicle_no', __('shared/permit.vehicle_number'), 10) !!} placeholder="B 1234 XYZ" value="{{ $detail->vehicle_no ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <div class="permit-actions">
                            @if ($is_admin && $isEdit)
                                <div class="permit-status">
                                    <label class="form-label mb-1" for="set_status">{{ __('shared/permit.status_after_save') }}</label>
                                    <select name="set_status" id="set_status" class="form-select">
                                        <option value="">{{ __('shared/permit.modify') }}</option>
                                        @foreach ($statuses as $code => $label)
                                            <option value="{{ $code }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <span class="hint">
                                <span class="d-block"><span class="req">*</span> {{ __('common.required_fields') }}</span>
                                <div class="form-note text-danger mt-0 mb-3">
                                        <span class="d-block mt-1"><i class="cil-info"></i>
                                        {!! __('shared/permit.office_hours_note', ['hours' => '<strong>' . e($office_hours) . '</strong>']) !!}
                                        </span>
                                    </div>
                            </span>
                            <button type="button" class="btn btn-outline-secondary" id="btnReset">
                                <i class="cil-reload"></i><span>{{ __('common.reset') }}</span>
                            </button>
                            <button type="submit" class="btn btn-primary" id="btnSave" @unless($isEdit) disabled @endunless>
                                <i class="cil-send"></i><span>{{ $isEdit ? __('shared/permit.save_changes') : __('common.submit') }}</span>
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
    // Teks UI sesuai bahasa aktif; placeholder :field / :type / :no diganti lewat t()
    @php
        $jsLang = [
            'permit_information'   => __('shared/permit.section_info'),
            'save_changes'         => __('shared/permit.save_changes'),
            'submit'               => __('common.submit'),
            'submit_type'          => __('shared/permit.submit_type'),
            'failed_load_units'    => __('shared/permit.failed_load_units'),
            'remove_row'           => __('shared/permit.remove_row'),
            'this_field'           => __('shared/permit.this_field'),
            'field_required'       => __('shared/permit.field_required'),
            'field_empty'          => __('shared/permit.field_empty'),
            'end_date_before'      => __('shared/permit.end_date_before'),
            'end_time_same'        => __('shared/permit.end_time_same'),
            'complete_fields'      => __('shared/permit.complete_fields'),
            'permit_type'          => __('shared/permit.permit_type'),
            'confirm_submit_title' => __('shared/permit.confirm_submit_title'),
            'confirm_save_title'   => __('shared/permit.confirm_save_title'),
            'confirm_submit_html'  => __('shared/permit.confirm_submit_html'),
            'confirm_update_html'  => __('shared/permit.confirm_update_html'),
            'yes_submit'           => __('shared/permit.yes_submit'),
            'yes_save'             => __('shared/permit.yes_save'),
            'cancel'               => __('common.cancel'),
            'submitting'           => __('shared/permit.submitting'),
            'submitted_title'      => __('shared/permit.submitted_title'),
            'updated_title'        => __('shared/permit.updated_title'),
            'redirect_history'     => __('shared/permit.redirect_history'),
            'ok'                   => __('common.ok'),
            'failed'               => __('common.failed'),
            'error'                => __('common.error'),
            'check_form'           => __('shared/permit.check_form'),
            'reset_title'          => __('shared/permit.reset_title'),
            'reset_text'           => __('shared/permit.reset_text'),
            'yes_reset'            => __('shared/permit.yes_reset'),
            'ph_worker_name'       => __('shared/permit.ph_worker_name'),
            'worker_name'          => __('shared/permit.worker_name'),
            'ph_activity'          => __('shared/permit.ph_activity'),
            'activity'             => __('shared/permit.activity'),
            'ph_tools'             => __('shared/permit.ph_tools'),
            'tools_ppe'            => __('shared/permit.tools_ppe'),
            'ph_goods'             => __('shared/permit.ph_goods'),
            'type_of_goods'        => __('shared/permit.type_of_goods'),
            'ph_qty'               => __('shared/permit.ph_qty'),
            'quantity'             => __('shared/permit.quantity'),
            'optional'             => __('common.optional'),
        ];
    @endphp
    var LANG = @json($jsLang);

    function t(text, params) {
        $.each(params || {}, function (key, value) {
            text = text.split(':' + key).join(value);
        });
        return text;
    }
    // Baris tersimpan (ubah permit): nama pekerja (W) atau barang (I/O), dan kegiatan (W)
    var EDIT_LINES = @json(array_values((array) $lines));
    var EDIT_TOOLS = @json(array_values((array) $tools));
    var GOODS_TIME = ['22:00', '10:00'];   // jam default keluar/masuk barang
    var MIN_SPINNER_MS = 800;   // overlay tampil minimal segini supaya tidak berkedip

    var $form     = $('#frmPermit');
    var $type     = $('#permit_type');
    var $tenant   = $('#tenant_no');
    var $lot      = $('#lot_no');
    var $floor    = $('#floor');
    var $permitNo = $('#permit_no');
    var $btnSave  = $('#btnSave');
    var $spinner  = $('#overlaySpinner');

    var sections = {
        location: $('#sectionLocation'),
        work:     $('#sectionWork'),
        goods:    $('#sectionGoods'),
        schedule: $('#sectionSchedule'),
        lines:    $('#sectionLines'),
        tools:    $('#sectionTools'),
        items:    $('#sectionItems'),
        sender:   $('#sectionSender')
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
        var isWork  = type === 'W';
        var isGoods = chosen && !isWork;

        $('#permitTitle').text(chosen ? TYPES[type] : LANG.permit_information);
        $('#permitEmpty').prop('hidden', chosen);

        setVisible(sections.location, chosen);
        setVisible(sections.schedule, chosen);
        setVisible(sections.work,     isWork);
        setVisible(sections.lines,    isWork);
        setVisible(sections.tools,    isWork);
        setVisible(sections.goods,    isGoods);
        setVisible(sections.items,    isGoods);
        setVisible(sections.sender,   isGoods);
        setVisible($('.time-field'),  chosen);
        setVisible($('.shift-field'), isWork);
        setVisible($('.goods-field'), isGoods);

        // Permit barang baru: jam default 22:00 - 10:00 (sesuai aturan), boleh diubah
        if (isGoods && !IS_EDIT) {
            $('#start_time').val(GOODS_TIME[0]);
            $('#end_time').val(GOODS_TIME[1]);
        }

        // setVisible() meng-enable semua input; kembalikan status tombol hapus & jam kerja
        grids.workers.renumber();
        grids.tools.renumber();
        grids.items.renumber();
        applyShift();

        if (IS_EDIT) {
            $btnSave.prop('disabled', false).find('span').text(LANG.save_changes);
        } else {
            $btnSave.prop('disabled', !chosen)
                .find('span').text(chosen ? t(LANG.submit_type, { type: TYPES[type] }) : LANG.submit);
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
                toast('error', LANG.failed_load_units);
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
    // Datepicker dd/mm/yyyy (sama dengan History Invoice); nilai dibandingkan lewat getDate
    function pickerDate(sel) {
        return $(sel).val() ? $(sel).datepicker('getDate') : null;
    }

    $('#start_date').on('change', function () {
        var start = pickerDate('#start_date'), end = pickerDate('#end_date');
        $('#end_date').datepicker('setStartDate', start || false);
        if (start && (!end || end < start)) {
            $('#end_date').datepicker('update', start);
        }
    });

    // ---------------------------------------------------------------
    // Jam Kerja: 10:00-22:00 / 22:00-10:00 mengisi jam otomatis, Other = isi sendiri
    // ---------------------------------------------------------------
    function applyShift() {
        var $opt = $('input[name="work_shift"]:checked');
        var fixed = $type.val() === 'W' && $opt.length && $opt.val() !== 'O';

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
    // Tabel baris (pekerja, kegiatan & peralatan, barang).
    // cols: {name: nama field (dikirim sebagai name[]), key: kunci data tersimpan,
    //        max, placeholder, label: diisi = wajib}
    // ---------------------------------------------------------------
    function makeGrid(tableSel, countSel, addBtnSel, cols) {
        var $body = $(tableSel + ' tbody');

        function renumber() {
            var $rows = $body.children('tr');
            $rows.each(function (i) {
                $(this).find('.line-no').text(i + 1);
            });
            $rows.find('.btn-del').prop('disabled', $rows.length <= 1);
            $(countSel).text($rows.length);
        }

        function add(focus, data) {
            var $tr = $('<tr><td class="line-no"></td></tr>');

            cols.forEach(function (c) {
                var $input = $('<input type="text" class="form-control" autocomplete="off">')
                    .attr({ name: c.name + '[]', maxlength: c.max, placeholder: c.placeholder || '' });
                if (c.label) {
                    $input.attr('data-req', c.label);
                }
                if (data) {
                    $input.val(typeof data === 'string' ? data : (data[c.key] || ''));
                }
                $('<td>').append(
                    $('<div class="form-control-wrap">').append($input, '<div class="invalid-feedback"></div>')
                ).appendTo($tr);
            });

            $tr.append('<td class="line-act"><button type="button" class="btn btn-del" title="' + escapeHtml(LANG.remove_row) + '"><i class="cil-trash"></i></button></td>');
            $body.append($tr);
            renumber();

            if (focus) {
                $tr.find('input').first().trigger('focus');
            }
        }

        function reset(rows) {
            $body.empty();
            if (rows && rows.length) {
                rows.forEach(function (r) { add(false, r); });
            } else {
                add(false);
            }
        }

        $(addBtnSel).on('click', function () {
            add(true);
        });

        $body.on('click', '.btn-del', function () {
            if ($body.children('tr').length <= 1) {
                return;
            }
            $(this).closest('tr').remove();
            renumber();
        });

        // Enter: pindah ke kolom berikutnya; di kolom terakhir baris terakhir -> baris baru
        $body.on('keydown', 'input', function (e) {
            if (e.key !== 'Enter') {
                return;
            }
            e.preventDefault();
            var $inputs = $body.find('input');
            var idx = $inputs.index(this);
            if (idx === $inputs.length - 1) {
                add(true);
            } else {
                $inputs.eq(idx + 1).trigger('focus');
            }
        });

        return { add: add, renumber: renumber, reset: reset };
    }

    var grids = {
        workers: makeGrid('#tblLines', '#lineCount', '#btnAddLine', [
            { name: 'worker_name', max: 50, placeholder: LANG.ph_worker_name, label: LANG.worker_name }
        ]),
        tools: makeGrid('#tblTools', '#toolCount', '#btnAddTool', [
            { name: 'tool_activity', key: 'activity',  max: 100, placeholder: LANG.ph_activity, label: LANG.activity },
            { name: 'tool_name',     key: 'tool_name', max: 100, placeholder: LANG.ph_tools, label: LANG.tools_ppe },
            { name: 'tool_remarks',  key: 'remarks',   max: 255, placeholder: LANG.optional }
        ]),
        items: makeGrid('#tblItems', '#itemCount', '#btnAddItem', [
            { name: 'item_name',    key: 'item_name', max: 100, placeholder: LANG.ph_goods, label: LANG.type_of_goods },
            { name: 'item_qty',     key: 'item_qty',  max: 20,  placeholder: LANG.ph_qty, label: LANG.quantity },
            { name: 'item_remarks', key: 'remarks',   max: 255, placeholder: LANG.optional }
        ])
    };

    function loadGrids() {
        var type = IS_EDIT ? $type.val() : '';
        grids.workers.reset(type === 'W' ? EDIT_LINES : []);
        grids.tools.reset(type === 'W' ? EDIT_TOOLS : []);
        grids.items.reset(type && type !== 'W' ? EDIT_LINES : []);
    }

    loadGrids();

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
                fail($(this), t(LANG.field_required, { field: $(this).data('label') || LANG.this_field }));
            }
        });

        var start = pickerDate('#start_date'), end = pickerDate('#end_date');
        if (start && end && end < start) {
            fail($('#end_date'), LANG.end_date_before);
        }

        // boleh lewat tengah malam (22:00 - 10:00), asal tidak sama
        var st = $('#start_time').val(), et = $('#end_time').val();
        if (st && et && et === st) {
            fail($('#end_time'), LANG.end_time_same);
        }

        // Kolom wajib di tabel baris (yang tampil saja)
        $form.find('.permit-lines input[data-req]:enabled').each(function () {
            if ($.trim($(this).val()) === '') {
                fail($(this), t(LANG.field_empty, { field: $(this).data('req') }));
            }
        });

        if ($first) {
            scrollTo($first);
            toast('error', LANG.complete_fields);
            return false;
        }

        return true;
    }

    // Pesan validasi dari server ({field: [msg]}); field baris berbentuk item_name.0, tool_name.2, ...
    function showServerErrors(errors) {
        var $first = null;

        $.each(errors, function (key, msgs) {
            var msg = $.isArray(msgs) ? msgs[0] : msgs;
            var m = /^(\w+)\.(\d+)$/.exec(key);
            var $el;

            if (m) {
                $el = $form.find('[name="' + m[1] + '[]"]:enabled').eq(parseInt(m[2], 10));
            } else if (key === 'work_shift') {
                $el = $('#start_time');
            } else {
                $el = $form.find('[name="' + key + '"]:enabled');
                if (!$el.length) {
                    $el = $form.find('[name="' + key + '[]"]:enabled').first();
                }
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
    $form.on('submit', function (e) {
        e.preventDefault();

        if (!TYPES[$type.val()]) {
            setError($type, t(LANG.field_required, { field: LANG.permit_type }));
            return;
        }

        if (!validate()) {
            return;
        }

        Swal.fire({
            title: t(IS_EDIT ? LANG.confirm_save_title : LANG.confirm_submit_title, { type: TYPES[$type.val()] }),
            html: t(escapeHtml(IS_EDIT ? LANG.confirm_update_html : LANG.confirm_submit_html), { no: '<strong>' + escapeHtml($permitNo.val() || '-') + '</strong>' }),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: IS_EDIT ? LANG.yes_save : LANG.yes_submit,
            cancelButtonText: LANG.cancel,
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
        $('#overlaySpinnerText').text(LANG.submitting);
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
            data: $form.serialize(),
            dataType: 'json'
        }).done(function (res) {
            finish(function () {
                if (res.status === 'OK') {
                    Swal.fire({
                        title: IS_EDIT ? LANG.updated_title : LANG.submitted_title,
                        html: escapeHtml(res.pesan) + '<br><small class="text-body-secondary">' + escapeHtml(LANG.redirect_history) + '</small>',
                        icon: 'success',
                        confirmButtonText: LANG.ok,
                        allowOutsideClick: false
                    }).then(function () {
                        window.location.href = URLS.history;
                    });
                } else {
                    $btnSave.prop('disabled', false);
                    Swal.fire({ title: LANG.failed, text: res.pesan, icon: 'error' });
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
                    title: xhr.status === 422 ? LANG.check_form : LANG.error,
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
            title: LANG.reset_title,
            text: LANG.reset_text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: LANG.yes_reset,
            cancelButtonText: LANG.cancel,
            reverseButtons: true
        }).then(function (result) {
            if (!result.value) {
                return;
            }
            $form[0].reset();
            $form.find('.js-select2').val('').trigger('change');
            loadGrids();
            $('#work_shift_D').prop('checked', true);
            $('#noteCount').text('0');
            $('#start_date, #end_date').datepicker('update', '');
            $('#end_date').datepicker('setStartDate', false);
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
