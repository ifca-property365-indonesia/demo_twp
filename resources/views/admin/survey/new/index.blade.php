@extends('admin.template.layout2.base')
@section('title', __('admin/survey.question_entry'))
@section('content')
<div class="page-body">
    <div>
        <div class="page-block">
            <div class="page-head">
                <div class="page-head-row">
                    <div class="page-head-content">
                        <h3 class="page-title">{{ __('admin/survey.question_entry') }}</h3>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-underline-border mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-coreui-toggle="tab" href="#t_draft"><i class="cil-pencil"></i> &nbsp;{{ __('admin/survey.draft_survey') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-coreui-toggle="tab" href="#t_published"><i class="cil-task"></i> &nbsp;{{ __('admin/survey.published_survey') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- TAB DRAFT -->
                        <div class="tab-pane active" id="t_draft">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered w-100" id="tbldraft">
                                    <thead>
                                    <tr>
                                        <th>{{ __('admin/survey.no') }}</th>          
                                        <th>{{ __('admin/survey.survey_title') }}</th>
                                        <th>{{ __('common.description') }}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        
                        <!-- TAB PUBLISHED -->
                        <div class="tab-pane" id="t_published">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered w-100" id="tblpublished">
                                    <thead>
                                    <tr>
                                        <th>{{ __('admin/survey.no') }}</th>          
                                        <th>{{ __('admin/survey.survey_title') }}</th>
                                        <th>{{ __('admin/survey.publish_date') }}</th>
                                        <th>{{ __('admin/survey.expired_date') }}</th>
                                        <th width="20%">{{ __('common.action') }}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div><!-- . -->
        </div> <!-- page-block -->
    </div>
</div>

<script type="text/javascript">
var tbldraft;
    var tblpublished;

    $(function(){
        // DATATABLE: PUBLISHED SURVEY
        tblpublished = $('#tblpublished').DataTable({
            "language": { "decimal": ",", "thousands": "." },
            "dom": '<"toolbar tblsurvey_published">frtip',
            "serverSide": true,
            "ajax": {
                "url": "{{ url('/admin/usersurvey/all-published') }}",
                "type": "POST"
            },
            "columns": [
                {data: "row_number", name:"row_number", searchable:false},
                {data: "title", name:"title", searchable:true},
                {data: "publish_date", name:"publish_date", searchable:true,
                    render: function (data) { return FormatDateNew(data); }
                },
                {data: "expired_date", name:"expired_date", searchable:true,
                    render: function (data) { return FormatDateNew(data); }
                },
                {data: null, name: "action", searchable: false, orderable: false, render: function (data, type, row) { 
                    var btnEdit = '<button type="button" class="btn btn-info btn-sm btn-edit-dates me-1" data-id="' + row.id + '" data-title="' + row.title + '" data-publish="' + row.publish_date + '" data-expired="' + row.expired_date + '"><i class="cil-pencil"></i> {{ __('common.edit') }}</button>';
                    var btnDelete = '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' + row.id + '"><i class="cil-trash"></i> {{ __('common.delete') }}</button>';
                    return btnEdit + btnDelete; 
                }}
            ]
        });

        // DATATABLE: DRAFT SURVEY
        tbldraft = $('#tbldraft').DataTable({
            "language": { "decimal": ",", "thousands": "." },
            "dom": '<"toolbar tbldraft">frtip',
            "select": true,
            "order": [[ 0, 'asc' ]],
            "serverSide": true,
            "ajax": {
                "url": "{{ url('/admin/usersurvey/all-draft') }}",
                "type": "POST"
            },
            "columns": [
                {data: "row_number", name:"row_number", searchable:false},
                {data: "title", name:"title", searchable:true},
                {data: "description", name:"description", searchable:true},
                {data: "id", name:"id", visible:false}
            ]
        });

        // TOOLBAR BUTTONS UNTUK DRAFT
        $("div.tbldraft").html(
            '<button id="addparam" class="btn btn-sm btn-primary">{{ __('common.add') }}</button>&nbsp;'+
            '<button id="editparam" class="btn btn-sm btn-info">{{ __('common.edit') }}</button>&nbsp;'+
            '<button id="deleteparam" class="btn btn-sm btn-danger">{{ __('common.delete') }}</button>&nbsp;'+
            '<button id="publishparam" class="btn btn-sm btn-secondary">{{ __('common.publish') }}</button>&nbsp;'
        );

        // ROW SELECTION (DRAFT)
        tbldraft.on('click', 'tr', function() {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            } else {
                tbldraft.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });
    
        // ACTION: ADD
        $('#addparam').click(function(){
            block(true,'#modalbodyxl');
            $('#modalxl').modal({backdrop: 'static', keyboard: false});
            $('#modaltitlexl').addClass('white').html(@json(__('admin/survey.add_new_survey')));
            $('.modal-footer').html('<button type="button" class="btn btn-sm btn-primary" id="savefrmxl">{{ __('common.save') }}</button><button type="button" class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">{{ __('common.close') }}</button>');
            $('#modalbodyxl').html("");
            $('#modalbodyxl').load("{{ url('/admin/usersurvey/create') }}");
            $('#modalxl').data('id', 0);
            $('#modalxl').data('form', 'add');
            $('#modalxl').modal('show');
        });

        // ACTION: EDIT (DRAFT SURVEY)
        $('#editparam').click(function(){
            var rows = tbldraft.rows('.selected').indexes();
            if (rows.length < 1) {
                Swal.fire(@json(__('common.information')), @json(__('admin/survey.select_row_to_edit')), "warning");
                return;
            }
            
            var data = tbldraft.rows(rows).data();
            var survey_id = data[0].id;

            block(true, '#modalbodyxl');
            $('#modalxl').modal({backdrop: 'static', keyboard: false});
            $('#modaltitlexl').addClass('white').html(@json(__('admin/survey.edit_survey')));
            $('.modal-footer').html('<button type="button" class="btn btn-sm btn-primary" id="savefrmxl">{{ __('common.update') }}</button><button type="button" class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">{{ __('common.close') }}</button>');
            $('#modalbodyxl').html("");
            $('#modalbodyxl').load("{{ url('/admin/usersurvey/edit') }}/" + survey_id);
            $('#modalxl').data('id', survey_id);
            $('#modalxl').data('form', 'edit');
            $('#modalxl').modal('show');
        });

        // ACTION: PUBLISH
        $('#publishparam').click(function(){
            var rows = tbldraft.rows('.selected').indexes();
            if (rows.length < 1) {
                Swal.fire(@json(__('common.information')), @json(__('admin/survey.select_row')), "warning");
                return;
            } 
            var data = tbldraft.rows(rows).data();
            var survey_id = data[0].id;
            var title = data[0].title;

            Swal.fire({
                title: @json(__('admin/survey.publish_title_confirm')).replace(':title', function(){ return title; }),
                text: @json(__('admin/survey.publish_moved')),
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: @json(__('admin/survey.yes_publish'))
            }).then(function(a){
                if (a.value == true) {
                    $('#modalbodyxl').html("");
                    $('#modalxl').modal({backdrop: 'static', keyboard: false});
                    $('.modal-footer').html('<button type="button" class="btn btn-sm btn-success" id="savefrm_publish">{{ __('admin/survey.confirm_publish') }}</button><button type="button" class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">{{ __('common.close') }}</button>');
                    $('#modaltitlexl').addClass('white').html(@json(__('admin/survey.publish_survey')));
                    $('#modalbodyxl').load("{{ url('/admin/usersurvey/publish-form') }}/" + survey_id);
                    $('#modalxl').data('id', survey_id);
                    $('#modalxl').data('form', 'publish');
                    $('#modalxl').modal('show');
                }
            });
        });

        // ACTION: DELETE (DRAFT)
        $('#deleteparam').click(function(){
            var rows = tbldraft.rows('.selected').indexes();
            if (rows.length < 1) {
                Swal.fire(@json(__('common.information')), @json(__('admin/survey.select_row')), "warning");
                return;
            } 
            var data = tbldraft.rows(rows).data();
            DeleteSurvey(data[0].id);
        });   

        // Tanggal: field datepicker dd/mm/yyyy (sama dengan History Invoice), data yyyy-mm-dd
        function isoToDmy(iso) {
            return iso ? iso.substr(8, 2) + '/' + iso.substr(5, 2) + '/' + iso.substr(0, 4) : '';
        }
        function pickerIso(sel) {
            var d = $(sel).val() ? $(sel).datepicker('getDate') : null;
            if (!d) { return ''; }
            var p = function (n) { return (n < 10 ? '0' : '') + n; };
            return d.getFullYear() + '-' + p(d.getMonth() + 1) + '-' + p(d.getDate());
        }

        // ACTION: EDIT DATES (PUBLISHED SURVEY)
        $('#tblpublished').on('click', '.btn-edit-dates', function () {
            var id = $(this).data('id');
            var title = $(this).data('title');
            var publish_date = $(this).data('publish') ? $(this).data('publish').substring(0, 10) : '';
            var expired_date = $(this).data('expired') ? $(this).data('expired').substring(0, 10) : '';
            
            var today = new Date();
            today.setHours(0,0,0,0);
            // tanggal lokal (toISOString memakai UTC, bisa mundur sehari di WIB)
            var todayStr = moment(today).format('DD/MM/YYYY');

            var pubDate = new Date(publish_date);
            pubDate.setHours(0,0,0,0);

            // CEK APAKAH PUBLISH DATE SUDAH/SEDANG BERJALAN (<= HARI INI)
            var isPublishDisabled = (pubDate <= today);

            var htmlForm = `
                <form id="frmUpdateDates">
                    <input type="hidden" name="id" value="${id}">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin/survey.survey_title') }}</label>
                        <input type="text" class="form-control" value="${title}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin/survey.publish_date') }}</label>
                        <div class="form-control-wrap">
                            <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                            <input type="text" class="form-control date-picker" data-date-format="dd/mm/yyyy" autocomplete="off" name="publish_date" id="edit_publish_date" value="${isoToDmy(publish_date)}" ${isPublishDisabled ? 'disabled style="background-color: #e9ecef; cursor: not-allowed;"' : ''} required>
                        </div>
                        
                        ${isPublishDisabled ? `
                            <div class="text-danger fw-bold mt-1" style="font-size: 12px;">
                                <i class="cil-lock-locked"></i> {{ __('admin/survey.publish_date_locked') }}
                            </div>
                        ` : ''}
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('admin/survey.expired_date') }}</label>
                        <div class="form-control-wrap">
                            <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                            <input type="text" class="form-control date-picker" data-date-format="dd/mm/yyyy" autocomplete="off" name="expired_date" id="edit_expired_date" value="${isoToDmy(expired_date)}" required>
                        </div>
                        
                        <!-- RED NOTIF REAL-TIME EXPIRED DATE -->
                        <div id="expired_date_error" class="text-danger fw-bold mt-1" style="display: none; font-size: 12px;">
                            <i class="cil-warning"></i> {{ __('admin/survey.expired_before_today_on', ['date' => '${todayStr}']) }}
                        </div>
                    </div>
                </form>
            `;

            $('#modalbodyxl').html(htmlForm);
            $('#modalxl').modal({backdrop: 'static', keyboard: false});
            $('#modaltitlexl').addClass('white').html(@json(__('admin/survey.edit_published_dates')));
            $('.modal-footer').html('<button type="button" class="btn btn-sm btn-primary" id="btnSaveUpdatedDates">{{ __('admin/survey.update_dates') }}</button><button type="button" class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">{{ __('common.close') }}</button>');
            $('#modalxl').modal('show');

            // datepicker (expired minimal hari ini, seperti atribut min sebelumnya)
            $('#edit_publish_date, #edit_expired_date').datepicker({
                format: 'dd/mm/yyyy', autoclose: true, todayHighlight: true, orientation: 'bottom auto'
            });
            $('#edit_expired_date').datepicker('setStartDate', today);
        });

        // EVENT LISTENER: CEK REAL-TIME SAAT USER UBAH EXPIRED DATE
        $(document).on('change input', '#edit_expired_date', function() {
            var iso = pickerIso(this);
            if (!iso) { return; }
            var selectedDate = new Date(iso + 'T00:00:00');

            var today = new Date();
            today.setHours(0,0,0,0);

            if (selectedDate < today) {
                $('#expired_date_error').slideDown();
                $('#btnSaveUpdatedDates').prop('disabled', true); // Kunci tombol submit
                $(this).addClass('is-invalid'); // Border input merah
            } else {
                $('#expired_date_error').slideUp();
                $('#btnSaveUpdatedDates').prop('disabled', false); // Buka kunci tombol submit
                $(this).removeClass('is-invalid');
            }
        });

        // SUBMIT EDIT DATES VIA AJAX
        $(document).on('click', '#btnSaveUpdatedDates', function() {
            var id = $('input[name="id"]').val();
            // dikirim sebagai yyyy-mm-dd (field menampilkan dd/mm/yyyy)
            var publish_date = pickerIso('#edit_publish_date');
            var expired_date = pickerIso('#edit_expired_date');

            var today = new Date();
            today.setHours(0,0,0,0);

            var expDate = new Date(expired_date + 'T00:00:00');

            // Validasi Expired Date Minimal Hari Ini
            if (!expired_date) {
                Swal.fire(@json(__('common.information')), @json(__('admin/survey.expired_required')), "warning");
                return;
            }

            if (expDate < today) {
                Swal.fire(@json(__('common.information')), @json(__('admin/survey.expired_before_today')), "warning");
                return;
            }

            if (publish_date && new Date(publish_date + 'T00:00:00') > expDate) {
                Swal.fire(@json(__('common.information')), @json(__('admin/survey.publish_after_expired')), "warning");
                return;
            }

            block(true, '#modalbodyxl');
            $.ajax({
                url: "{{ url('/admin/usersurvey/update-dates') }}",
                type: "POST",
                data: {
                    id: id,
                    publish_date: publish_date,
                    expired_date: expired_date,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(event) {
                    block(false, '#modalbodyxl');
                    if (event.status == 'OK') {
                        Swal.fire(@json(__('common.information')), event.message || @json(__('admin/survey.dates_updated')), "success");
                        $('#modalxl').modal('hide');
                        tblpublished.ajax.reload(null, false);
                    } else {
                        Swal.fire(@json(__('common.information')), event.message || @json(__('admin/survey.dates_update_failed')), "error");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    block(false, '#modalbodyxl');
                    Swal.fire(@json(__('common.information')), textStatus + ' : ' + errorThrown, "warning");
                }
            });
        });

    });
    
    // FUNCTION: DELETE AJAX
    function DeleteSurvey(id) {
        Swal.fire({
            title: @json(__('common.are_you_sure')),
            text: @json(__('admin/survey.revert_warning')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: @json(__('admin/survey.yes_delete'))
        }).then(function(result) {
            if (result.value == true) {
                block(true, '.page-body');
                $.ajax({
                    url : "{{ url('/admin/usersurvey/delete') }}",
                    type: "POST",
                    data: { id: id, _token: '{{ csrf_token() }}' },
                    dataType: "json",
                    success: function(event) {
                        block(false, '.page-body');
                        if (event.status == 'OK') {
                            Swal.fire(@json(__('common.information')), event.message, "success");
                            tbldraft.ajax.reload(null, false); 
                            tblpublished.ajax.reload(null, false); 
                        } else {
                            Swal.fire(@json(__('common.information')), event.message, "error");
                        }
                    },                    
                    error: function(jqXHR, textStatus, errorThrown){        
                        block(false, '.page-body');
                        Swal.fire(@json(__('common.information')), textStatus + ' : ' + errorThrown, "warning");
                    }
                });
            }
        });
    }

    // ACTION: DELETE (PUBLISHED)
    $('#tblpublished').on('click', '.btn-delete', function () {
        var id = $(this).data('id');
        DeleteSurvey(id);
    });
</script>
@endsection