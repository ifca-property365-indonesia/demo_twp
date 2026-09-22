@extends('tenant.template.base')
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Permit</h3>
                </div>
            </div>
        </div>

        <div class="card card-preview">
            <div class="card-inner">

                <div class="card-title-group">
                    <div class="card-title">
                        <h6 class="title">
                            <span class="mr-2" id="permitTitle">Permit</span>
                        </h6>
                    </div>
                </div>

                <br/>

                <form class="form-horizontal"
                      id="frm"
                      enctype="multipart/form-data"
                      method="POST"
                      action="">

                    @csrf

                    <div class="col-md-12">

                        <!-- PERMIT TYPE -->
                        <div class="form-group row">
                            <div class="col-6">
                                <label class="col-xs-2 form-label">
                                    Permit Type <span class="text-danger">*</span>
                                </label>

                                <div class="col-xs-10">
                                    <select name="permit_type"
                                            id="permit_type"
                                            class="form-control select2"
                                            data-placeholder="Choose a Permit Type">

                                        <option value="">---Choose a Permit Type---</option>
                                        <option value="W">Work Permit</option>
                                        <option value="I">Entry Permit of Goods</option>
                                        <option value="O">Exit Permit of Goods</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="col-xs-2 form-label">
                                    Permit Number
                                </label>

                                <div class="col-xs-10">
                                    <input type="text"
                                           class="form-control"
                                           value="{{ $letter_no }}"
                                           name="permit_no"
                                           id="permit_no"
                                           readonly="readonly" />
                                </div>
                            </div>
                        </div>


                        <!-- APPLICANT -->
                        <div class="form-group row">

                            <div class="col-6">
                                <label class="col-xs-2 form-label">
                                    Applicant <span class="text-danger">*</span>
                                </label>

                                <div class="col-xs-10">
                                    <input type="text"
                                           class="form-control"
                                           value="{{ $Tuname }}"
                                           name="pemohon"
                                           id="pemohon"
                                           readonly="readonly" />
                                </div>
                            </div>

                            <div class="col-6">
                                <label class="col-xs-2 form-label">
                                    Phone Number <span class="text-danger">*</span>
                                </label>

                                <div class="col-xs-10">
                                    <input type="text"
                                           class="form-control"
                                           value="{{ $datatenant->handphone ?? '' }}"
                                           name="handphone"
                                           id="handphone"
                                           readonly="readonly" />
                                </div>
                            </div>

                        </div>


                        <!-- ========================================== -->
                        <!-- WORK PERMIT -->
                        <!-- ========================================== -->

                        <div id="sectionWorkPermit" style="display:none;">

                            <div class="form-group row">

                                <div class="col-6">
                                    <label class="col-xs-2 form-label">
                                        Person in Charge <span class="text-danger">*</span>
                                    </label>

                                    <div class="col-xs-10">
                                        <input type="text"
                                               class="form-control"
                                               id="incharge"
                                               name="incharge"
                                               value="" />
                                    </div>
                                </div>

                                <div class="col-6">
                                    <label class="col-xs-2 form-label">
                                        Contractor Name <span class="text-danger">*</span>
                                    </label>

                                    <div class="col-xs-10">
                                        <input type="text"
                                               class="form-control"
                                               id="contractor"
                                               name="contractor"
                                               value="" />
                                    </div>
                                </div>

                            </div>


                            <div class="form-group row">

                                <div class="col-6">
                                    <div class="row">

                                        <!-- Tenant -->
                                        <div class="col-4">
                                            <label class="form-label">
                                                Tenant<span class="text-danger">*</span>
                                            </label>

                                            <select name="tenant_no" id="tenant_no" class="form-control select2" data-placeholder="Choose a Tenant">
                                                {!! $combo_tenant !!}
                                            </select>
                                        </div>

                                        <!-- Unit -->
                                        <div class="col-4">
                                            <label class="form-label">
                                                Unit<span class="text-danger">*</span>
                                            </label>

                                            <select name="lot_no" id="lot_no" class="form-control select2" data-placeholder="Choose a Unit">
                                                <option></option>
                                            </select>
                                        </div>

                                        <!-- Floor -->
                                        <div class="col-4">
                                            <label class="form-label">
                                                Floor<span class="text-danger">*</span>
                                            </label>

                                            <input type="text"
                                                class="form-control"
                                                id="floor"
                                                name="floor"
                                                value=""
                                                readonly="readonly" />
                                        </div>

                                    </div>
                                </div>

                                <div class="col-6">
                                    <label class="col-xs-2 form-label">
                                        Job Type <span class="text-danger">*</span>
                                    </label>

                                    <div class="col-xs-10">
                                        <input type="text"
                                               class="form-control"
                                               id="job_type"
                                               name="job_type"
                                               value="" />
                                    </div>
                                </div>

                            </div>


                            <div class="form-group row">

                                <div class="col-6">
                                    <label class="col-xs-2 form-label">
                                        Work Tools <span class="text-danger">*</span>
                                    </label>

                                    <div class="col-xs-10">
                                        <input type="text"
                                               class="form-control"
                                               id="work_tool"
                                               name="work_tool"
                                               value="" />
                                    </div>
                                </div>

                                <div class="col-6">
                                    <label class="col-xs-2 form-label">
                                        Note <span class="text-danger">*</span>
                                    </label>

                                    <div class="col-xs-10">
                                        <input type="text"
                                               class="form-control"
                                               id="note"
                                               name="note"
                                               value="" />
                                    </div>
                                </div>

                            </div>


                            <!-- WORK DATE & TIME -->
                            <div class="form-group row">

                                <div class="col-6">
                                    <div class="row">

                                        <!-- Floor -->
                                        <div class="col-6">
                                            <label class="form-label">
                                                Start Date <span class="text-danger">*</span>
                                            </label>

                                            <input type="date"
                                                class="form-control"
                                                id="start_date"
                                                name="start_date"
                                                value="" />
                                        </div>

                                        <!-- Unit -->
                                        <div class="col-6">
                                            <label class="form-label">
                                                End Date<span class="text-danger">*</span>
                                            </label>

                                            <input type="date"
                                                class="form-control"
                                                id="end_date"
                                                name="end_date"
                                                value="" />
                                        </div>

                                    </div>
                                </div>
                                
                                <div class="col-6">
                                    <div class="row">

                                        <!-- Floor -->
                                        <div class="col-6">
                                            <label class="form-label">
                                                Start Time <span class="text-danger">*</span>
                                            </label>

                                            <input type="time"
                                                class="form-control"
                                                id="start_time"
                                                name="start_time"
                                                value="" />
                                        </div>

                                        <!-- Unit -->
                                        <div class="col-6">
                                            <label class="form-label">
                                                End Time <span class="text-danger">*</span>
                                            </label>

                                            <input type="time"
                                                class="form-control"
                                                id="end_time"
                                                name="end_time"
                                                value="" />
                                        </div>

                                    </div>
                                </div>


                            </div>


                            <!-- WORKERS -->
                            <div class="form-group row">

                                <div class="col-12">

                                    <label class="form-label">
                                        Workers <span class="text-danger">*</span>

                                        <button type="button"
                                                id="btnAddWorker"
                                                class="btn btn-primary">

                                            <em class="icon ni ni-plus"></em>
                                            Add Worker

                                        </button>
                                    </label>


                                    <div class="table-responsive">

                                        <table class="table table-bordered"
                                               id="workerTable">

                                            <thead>
                                                <tr>
                                                    <th width="5%">No.</th>
                                                    <th>Worker Name</th>
                                                    <th width="15%">Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>

                                        </table>

                                    </div>

                                    <br/>

                                </div>

                            </div>

                        </div>


                        <!-- ========================================== -->
                        <!-- ENTRY / EXIT PERMIT -->
                        <!-- ========================================== -->

                        <div id="sectionOtherPermit" style="display:none;">

                            <div class="form-group row">

                                <div class="col-6">
                                    <label class="col-xs-2 form-label">
                                        Company Name <span class="text-danger">*</span>
                                    </label>

                                    <div class="col-xs-10">
                                        <input type="text"
                                               class="form-control"
                                               id="company"
                                               name="company"
                                               value="" />
                                    </div>
                                </div>

                                <div class="col-6">
                                    <label class="col-xs-2 form-label">
                                        Owner Name <span class="text-danger">*</span>
                                    </label>

                                    <div class="col-xs-10">
                                        <input type="text"
                                               class="form-control"
                                               id="owner"
                                               name="owner"
                                               value="" />
                                    </div>
                                </div>

                            </div>


                            <div class="form-group row">

                                <div class="col-6">
                                    <div class="row">

                                        <!-- Tenant -->
                                        <div class="col-4">
                                            <label class="form-label">
                                                Tenant<span class="text-danger">*</span>
                                            </label>

                                            <select name="permit_tenant_no" id="permit_tenant_no" class="form-control select2" data-placeholder="Choose a Tenant">
                                                {!! $combo_tenant !!}
                                            </select>
                                        </div>

                                        <!-- Unit -->
                                        <div class="col-4">
                                            <label class="form-label">
                                                Unit<span class="text-danger">*</span>
                                            </label>

                                            <select name="permit_lot_no" id="permit_lot_no" class="form-control select2" data-placeholder="Choose a Unit">
                                                <option></option>
                                            </select>
                                        </div>

                                        <!-- Floor -->
                                        <div class="col-4">
                                            <label class="form-label">
                                                Floor<span class="text-danger">*</span>
                                            </label>

                                            <input type="text"
                                                class="form-control"
                                                id="permit_floor"
                                                name="permit_floor"
                                                value=""
                                                readonly="readonly" />
                                        </div>

                                    </div>
                                </div>

                                <div class="col-6">
                                    <label class="col-xs-2 form-label">
                                        Vehicle Number <span class="text-danger">*</span>
                                    </label>

                                    <div class="col-xs-10">
                                        <input type="text"
                                               class="form-control"
                                               id="vehicle_no"
                                               name="vehicle_no"
                                               value="" />
                                    </div>
                                </div>

                            </div>


                            
                            <div class="form-group row">

                                <div class="col-6">

                                    <label class="col-xs-2 form-label">
                                        Notes <span class="text-danger">*</span>
                                    </label>

                                    <div class="row">

                                        <div class="col-12">
                                            <input type="text"
                                                   class="form-control"
                                                   id="notes"
                                                   name="notes"
                                                   value="" />
                                        </div>

                                    </div>

                                </div>
                                
                                <div class="col-6">
                                    <div class="row">

                                        <!-- Floor -->
                                        <div class="col-6">
                                            <label class="form-label">
                                                Start Date <span class="text-danger">*</span>
                                            </label>

                                            <input type="date"
                                                class="form-control"
                                                id="permit_start_date"
                                                name="permit_start_date"
                                                value="" />
                                        </div>

                                        <!-- Unit -->
                                        <div class="col-6">
                                            <label class="form-label">
                                                End Date<span class="text-danger">*</span>
                                            </label>

                                            <input type="date"
                                                class="form-control"
                                                id="permit_end_date"
                                                name="permit_end_date"
                                                value="" />
                                        </div>

                                    </div>
                                </div>


                            </div>


                            <!-- ITEMS -->
                            <div class="form-group row">

                                <div class="col-12">

                                    <label class="form-label">
                                        Items <span class="text-danger">*</span>

                                        <button type="button"
                                                id="btnAddItem"
                                                class="btn btn-primary">

                                            <em class="icon ni ni-plus"></em>
                                            Add Item

                                        </button>
                                    </label>


                                    <div class="table-responsive">

                                        <table class="table table-bordered"
                                               id="itemTable">

                                            <thead>
                                                <tr>
                                                    <th width="5%">No.</th>
                                                    <th>Item Name</th>
                                                    <th width="15%">Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>

                                        </table>

                                    </div>

                                    <br/>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- SUBMIT -->
                    <div style="text-align:right;
                                margin-right:50px;
                                margin-top:20px">

                        <button type="button"
                                id="btnSave"
                                class="btn btn-primary">
                            Submit
                        </button>

                    </div>


                    <input type="hidden" name="entity" id="entity" value="{{ $entity_cd }}"/>
                    <input type="hidden" name="project" id="project" value="{{ $project_no }}" />

                </form>

            </div>
        </div>
    </div>


    <!-- SPINNER -->
    <div id="overlaySpinner" class="spinner-overlay">

        <div class="spinner-box">

            <div class="spinner"></div>

            <div class="loading-text">
                Processing, please wait...
            </div>

        </div>

    </div>


    <script>

    $(document).ready(function () {

        var workerIndex = 0;
        var itemIndex = 0;

        if ($.fn.select2) {
            $('.select2').select2();
        }


        // ==========================================
        // TOGGLE PERMIT
        // ==========================================

        function togglePermitFields() {

            var permitType = $('#permit_type').val();

            $('#sectionWorkPermit').hide();
            $('#sectionOtherPermit').hide();

            $('#permitTitle').text('Permit');

            if (permitType === 'W') {

                $('#sectionWorkPermit').show();
                $('#permitTitle').text('Work Permit');

            } else if (permitType === 'I') {

                $('#sectionOtherPermit').show();
                $('#permitTitle').text('Entry Permit of Goods');

            } else if (permitType === 'O') {

                $('#sectionOtherPermit').show();
                $('#permitTitle').text('Exit Permit of Goods');

            }
        }


        $('#permit_type').on('change', function () {
            togglePermitFields();
        });


        togglePermitFields();


        // ==========================================
        // WORKERS
        // ==========================================

        // Penomoran ulang + baris terakhir tidak boleh dihapus (minimal 1 pekerja)
        function updateWorkerNumber() {

            var rows = $('#workerTable tbody tr');

            rows.each(function (index) {

                $(this).find('.worker-number').text(index + 1);

            });

            rows.find('.btnDeleteWorker').prop('disabled', rows.length <= 1);

        }


        function addWorkerRow() {

            workerIndex++;

            var row = `
                <tr>

                    <td class="text-center worker-number">
                        ${workerIndex}
                    </td>

                    <td>
                        <input type="text"
                               class="form-control"
                               name="worker_name[]"
                               placeholder="Worker Name"
                               autocomplete="off">
                    </td>

                    <td class="text-center">

                        <button type="button"
                                class="btn btn-danger btn-sm btnDeleteWorker">

                            <em class="icon ni ni-trash"></em>
                            Delete

                        </button>

                    </td>

                </tr>
            `;

            $('#workerTable tbody').append(row);

            updateWorkerNumber();

        }


        $('#btnAddWorker').on('click', addWorkerRow);


        $(document).on('click', '.btnDeleteWorker', function () {

            if ($('#workerTable tbody tr').length <= 1) {
                alert('At least one worker is required.');
                return;
            }

            $(this).closest('tr').remove();

            updateWorkerNumber();

        });


        // Baris pertama selalu ada
        addWorkerRow();


        // ==========================================
        // ITEMS
        // ==========================================

        // Penomoran ulang + baris terakhir tidak boleh dihapus (minimal 1 barang)
        function updateItemNumber() {

            var rows = $('#itemTable tbody tr');

            rows.each(function (index) {

                $(this).find('.item-number').text(index + 1);

            });

            rows.find('.btnDeleteItem').prop('disabled', rows.length <= 1);

        }


        function addItemRow() {

            itemIndex++;

            var row = `
                <tr>

                    <td class="text-center item-number">
                        ${itemIndex}
                    </td>

                    <td>
                        <input type="text"
                               class="form-control"
                               name="item_name[]"
                               placeholder="Item Name"
                               autocomplete="off">
                    </td>

                    <td class="text-center">

                        <button type="button"
                                class="btn btn-danger btn-sm btnDeleteItem">

                            <em class="icon ni ni-trash"></em>
                            Delete

                        </button>

                    </td>

                </tr>
            `;

            $('#itemTable tbody').append(row);

            updateItemNumber();

        }


        $('#btnAddItem').on('click', addItemRow);


        $(document).on('click', '.btnDeleteItem', function () {

            if ($('#itemTable tbody tr').length <= 1) {
                alert('At least one item is required.');
                return;
            }

            $(this).closest('tr').remove();

            updateItemNumber();

        });


        // Baris pertama selalu ada
        addItemRow();


        // ==========================================
        // DATE VALIDATION
        // ==========================================

        $('#start_date').on('change', function () {

            $('#end_date').attr('min', $(this).val());

        });


        $('#permit_start_date').on('change', function () {

            $('#permit_end_date').attr('min', $(this).val());

        });

        // ==========================================
        // TENANT -> UNIT -> FLOOR
        // (pakai endpoint getLotNo milik ticket; floor diisi dari data-level unit)
        // ==========================================

        function bindTenantLot(tenantSel, lotSel, floorInp) {

            $(tenantSel).on('change', function () {

                var tenant_no = $(this).val();
                var ent = $(this).find(':selected').data('entity');
                var prj = $(this).find(':selected').data('project');

                $(lotSel).empty().append('<option></option>');
                $(floorInp).val('');

                if (tenant_no) {

                    $('#entity').val(ent);
                    $('#project').val(prj);

                    $.post("{{ url('tenant/ticket/getLotNo') }}",
                        {
                            "_token": "{{ csrf_token() }}",
                            id_tenancy: tenant_no
                        },
                        function (data) {
                            $(lotSel).empty().append(data);
                        }
                    );
                }
            });

            $(lotSel).on('change', function () {

                var lvl = $(this).find(':selected').data('level');

                $(floorInp).val(lvl !== undefined ? lvl : '');
            });
        }

        bindTenantLot('#tenant_no', '#lot_no', '#floor');
        bindTenantLot('#permit_tenant_no', '#permit_lot_no', '#permit_floor');

        // ==========================================
        // Button Submit
        // ==========================================

        $('#btnSave').on('click', function () {

            var permitType = $('#permit_type').val();

            if (permitType === 'W') {
                saveworkpermit();
            } else if (permitType === 'I' || permitType === 'O') {
                savepermitofgoods();
            } else {
                alert('Please select a Permit Type.');
            }

        });


        // ==========================================
        // Helper Validasi
        // ==========================================

        function validateFields(fields) {

            for (var i = 0; i < fields.length; i++) {

                if ($.trim($(fields[i].id).val()) === '') {
                    alert(fields[i].label + ' is required.');
                    $(fields[i].id).focus();
                    return false;
                }

            }

            return true;
        }

        function validateRows(tableId, inputName, label) {

            var rows = $(tableId + ' tbody tr');

            if (rows.length === 0) {
                alert('Please add at least one ' + label + '.');
                return false;
            }

            var valid = true;

            rows.each(function () {

                var input = $(this).find('input[name="' + inputName + '"]');

                if ($.trim(input.val()) === '') {
                    alert(label + ' name cannot be empty.');
                    input.focus();
                    valid = false;
                    return false;
                }

            });

            return valid;
        }


        // ==========================================
        // Save Work Permit
        // ==========================================

        function saveworkpermit() {

            var fields = [
                { id: '#tenant_no',  label: 'Tenant' },
                { id: '#permit_no',     label: 'Permit Number' },
                { id: '#lot_no',     label: 'Unit' },
                { id: '#incharge',   label: 'Person in Charge' },
                { id: '#contractor', label: 'Contractor Name' },
                { id: '#floor',      label: 'Floor' },
                { id: '#job_type',   label: 'Job Type' },
                { id: '#work_tool',  label: 'Work Tools' },
                { id: '#note',       label: 'Note' },
                { id: '#start_date', label: 'Start Date' },
                { id: '#end_date',   label: 'End Date' },
                { id: '#start_time', label: 'Start Time' },
                { id: '#end_time',   label: 'End Time' }
            ];

            if (!validateFields(fields)) return;

            if ($('#end_date').val() < $('#start_date').val()) {
                alert('End Date cannot be earlier than Start Date.');
                $('#end_date').focus();
                return;
            }

            if ($('#end_time').val() <= $('#start_time').val()) {
                alert('End Time must be later than Start Time.');
                $('#end_time').focus();
                return;
            }

            if (!validateRows('#workerTable', 'worker_name[]', 'Worker')) return;

            var datafrm = buildFormData('#sectionWorkPermit');

            console.log('=== WORK PERMIT FORM ===');
            console.log(datafrm);
            console.log(toObject(datafrm));

            submitPermit("{{ url('tenant/permit/workpermit') }}", datafrm);
        }


        // ==========================================
        // Save Permit of Goods (Entry / Exit)
        // ==========================================

        function savepermitofgoods() {

            var fields = [
                { id: '#permit_tenant_no',  label: 'Tenant' },
                { id: '#permit_lot_no',     label: 'Unit' },
                { id: '#company',           label: 'Company Name' },
                { id: '#owner',             label: 'Owner Name' },
                { id: '#permit_floor',      label: 'Floor' },
                { id: '#vehicle_no',        label: 'Vehicle Number' },
                { id: '#permit_start_date', label: 'Start Date' },
                { id: '#permit_end_date',   label: 'End Date' },
                { id: '#notes',             label: 'Notes' }
            ];

            if (!validateFields(fields)) return;

            if ($('#permit_end_date').val() < $('#permit_start_date').val()) {
                alert('End Date cannot be earlier than Start Date.');
                $('#permit_end_date').focus();
                return;
            }

            if (!validateRows('#itemTable', 'item_name[]', 'Item')) return;

            var datafrm = buildFormData('#sectionOtherPermit');

            console.log('=== PERMIT OF GOODS FORM (' + $('#permit_type').val() + ') ===');
            console.log(datafrm);
            console.log(toObject(datafrm));

            submitPermit("{{ url('tenant/permit/permitofgoods') }}", datafrm);
        }


        // ==========================================
        // Form Data
        // ==========================================

        // Ambil field umum + field di section yang aktif saja,
        // supaya input dari section yang hidden tidak ikut terkirim
        function buildFormData(sectionId) {

            var common = $('#frm')
                .find('input[name="_token"], #permit_type, #permit_no, #pemohon, #handphone, #entity, #project')
                .serializeArray();

            var section = $(sectionId).find(':input').serializeArray();

            return common.concat(section);
        }

        // Untuk console.log yang lebih mudah dibaca (name[] dikumpulkan jadi array)
        function toObject(datafrm) {

            var obj = {};

            $.each(datafrm, function (i, field) {

                if (field.name.slice(-2) === '[]') {
                    var key = field.name.slice(0, -2);
                    if (!obj[key]) obj[key] = [];
                    obj[key].push(field.value);
                } else {
                    obj[field.name] = field.value;
                }

            });

            return obj;
        }


        // ==========================================
        // AJAX Submit
        // ==========================================

        function submitPermit(url, datafrm) {

            console.log('POST -> ' + url);

            // Disable button
            $('#btnSave').prop('disabled', true);

            // Tampilkan loading
            $('#overlaySpinner').css('display', 'flex');

            // Simpan waktu mulai
            var startTime = Date.now();

            $.ajax({
                url: url,
                type: "POST",
                data: datafrm,
                dataType: "json",

                success: function (event) {

                    console.log('RESPONSE:', event);

                    // Minimal tampil 3 detik
                    var elapsed = Date.now() - startTime;
                    var remaining = Math.max(0, 3000 - elapsed);

                    setTimeout(function () {

                        $('#overlaySpinner').hide();

                        if (event.status == 'OK') {

                            Swal.fire({
                                title: "Information",
                                icon: "success",
                                text: event.pesan,
                                confirmButtonText: "OK"
                            }).then(function () {
                                window.location.href = "{{ url('/tenant/permit/history') }}";
                            });

                        } else {

                            $('#btnSave').prop('disabled', false);

                            Swal.fire({
                                title: "Information",
                                icon: "error",
                                text: event.pesan,
                                confirmButtonText: "OK"
                            });
                        }

                    }, remaining);
                },

                error: function (jqXHR, textStatus, errorThrown) {

                    console.log('ERROR:', jqXHR.status, jqXHR.responseText);

                    var elapsed = Date.now() - startTime;
                    var remaining = Math.max(0, 3000 - elapsed);

                    setTimeout(function () {

                        $('#overlaySpinner').hide();
                        $('#btnSave').prop('disabled', false);

                        Swal.fire({
                            title: "Error",
                            icon: "error",
                            text: (jqXHR.responseJSON && jqXHR.responseJSON.pesan)
                                    ? jqXHR.responseJSON.pesan
                                    : textStatus + ' Save : ' + errorThrown,
                            confirmButtonText: "OK"
                        });

                    }, remaining);
                }
            });
        }



    });

    </script>

@endsection