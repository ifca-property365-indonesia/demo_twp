@extends('admin.template.layout2.base')
@section('content')
<style type="text/css">
    .toolbar {
        float: left;
        margin-bottom: 1em;
    }
</style>
<div class="nk-content-body">
    <div class="components-preview wide-md mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title">Question Entry</h4>
                </div>
            </div>
            <div class="card card-preview">
                <div class="card-inner">
                    <ul class="nav nav-tabs mt-n3">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#t_draft"><em class="icon ni ni-edit"></em> &nbsp;Draft Survey</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#t_published"><em class="icon ni ni-list-check"></em> &nbsp;Published Survey</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- TAB DRAFT -->
                        <div class="tab-pane active" id="t_draft">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="tbldraft" width="100%">
                                    <thead>
                                    <tr>
                                        <th>No.</th>          
                                        <th>Survey Title</th>
                                        <th>Description</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        
                        <!-- TAB PUBLISHED -->
                        <div class="tab-pane" id="t_published">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="tblpublished" width="100%">
                                    <thead>
                                    <tr>
                                        <th>No.</th>          
                                        <th>Survey Title</th>
                                        <th>Publish Date</th>
                                        <th>Expired Date</th>
                                        <th width="20%">Action</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div><!-- .card-preview -->
        </div> <!-- nk-block -->
    </div>
</div>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
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
                    var btnEdit = '<button type="button" class="btn btn-info btn-sm btn-edit-dates mr-1" data-id="' + row.id + '" data-title="' + row.title + '" data-publish="' + row.publish_date + '" data-expired="' + row.expired_date + '"><em class="icon ni ni-edit"></em> Edit</button>';
                    var btnDelete = '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' + row.id + '"><em class="icon ni ni-trash"></em> Delete</button>';
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
            '<button id="addparam" class="btn btn-primary pull-up">Add</button>&nbsp;'+
            '<button id="editparam" class="btn btn-info pull-up">Edit</button>&nbsp;'+
            '<button id="deleteparam" class="btn btn-danger pull-up">Delete</button>&nbsp;'+
            '<button id="publishparam" class="btn btn-secondary pull-up">Publish</button>&nbsp;'
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
            $('#modaltitlexl').addClass('white').html('Add New Survey');
            $('.modal-footer').html('<button type="button" class="btn btn-primary" id="savefrmxl">Save</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
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
                Swal.fire("Information", 'Please select a row to edit', "warning");
                return;
            }
            
            var data = tbldraft.rows(rows).data();
            var survey_id = data[0].id;

            block(true, '#modalbodyxl');
            $('#modalxl').modal({backdrop: 'static', keyboard: false});
            $('#modaltitlexl').addClass('white').html('Edit Survey');
            $('.modal-footer').html('<button type="button" class="btn btn-primary" id="savefrmxl">Update</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
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
                Swal.fire("Information", 'Please select a row', "warning");
                return;
            } 
            var data = tbldraft.rows(rows).data();
            var survey_id = data[0].id;
            var title = data[0].title;

            Swal.fire({
                title: 'Publish "' + title + '"?',
                text: 'Published surveys will be moved to the Published tab.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Publish'
            }).then(function(a){
                if (a.value == true) {
                    $('#modalbodyxl').html("");
                    $('#modalxl').modal({backdrop: 'static', keyboard: false});
                    $('.modal-footer').html('<button type="button" class="btn btn-success" id="savefrm_publish">Confirm Publish</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#modaltitlexl').addClass('white').html('Publish Survey');
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
                Swal.fire("Information", 'Please select a row', "warning");
                return;
            } 
            var data = tbldraft.rows(rows).data();
            DeleteSurvey(data[0].id);
        });   

        // ACTION: EDIT DATES (PUBLISHED SURVEY)
        $('#tblpublished').on('click', '.btn-edit-dates', function () {
            var id = $(this).data('id');
            var title = $(this).data('title');
            var publish_date = $(this).data('publish') ? $(this).data('publish').substring(0, 10) : '';
            var expired_date = $(this).data('expired') ? $(this).data('expired').substring(0, 10) : '';
            
            var today = new Date();
            today.setHours(0,0,0,0);
            var todayStr = today.toISOString().split('T')[0];

            var pubDate = new Date(publish_date);
            pubDate.setHours(0,0,0,0);

            // CEK APAKAH PUBLISH DATE SUDAH/SEDANG BERJALAN (<= HARI INI)
            var isPublishDisabled = (pubDate <= today);

            var htmlForm = `
                <form id="frmUpdateDates">
                    <input type="hidden" name="id" value="${id}">
                    <div class="form-group">
                        <label class="form-label">Survey Title</label>
                        <input type="text" class="form-control" value="${title}" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Publish Date</label>
                        <input type="date" class="form-control" name="publish_date" id="edit_publish_date" value="${publish_date}" ${isPublishDisabled ? 'disabled style="background-color: #e9ecef; cursor: not-allowed;"' : ''} required>
                        
                        ${isPublishDisabled ? `
                            <div class="text-danger font-weight-bold mt-1" style="font-size: 12px;">
                                <em class="icon ni ni-lock-alt"></em> The publish date is active/running today and can no longer be modified!
                            </div>
                        ` : ''}
                    </div>

                    <div class="form-group">
                        <label class="form-label">Expired Date</label>
                        <input type="date" class="form-control" name="expired_date" id="edit_expired_date" value="${expired_date}" min="${todayStr}" required>
                        
                        <!-- RED NOTIF REAL-TIME EXPIRED DATE -->
                        <div id="expired_date_error" class="text-danger font-weight-bold mt-1" style="display: none; font-size: 12px;">
                            <em class="icon ni ni-alert-circle"></em> Expired date cannot be earlier than today (${todayStr})!
                        </div>
                    </div>
                </form>
            `;

            $('#modalbodyxl').html(htmlForm);
            $('#modalxl').modal({backdrop: 'static', keyboard: false});
            $('#modaltitlexl').addClass('white').html('Edit Published Survey Dates');
            $('.modal-footer').html('<button type="button" class="btn btn-primary" id="btnSaveUpdatedDates">Update Dates</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
            $('#modalxl').modal('show');
        });

        // EVENT LISTENER: CEK REAL-TIME SAAT USER UBAH EXPIRED DATE
        $(document).on('change input', '#edit_expired_date', function() {
            var selectedDate = new Date($(this).val());
            selectedDate.setHours(0,0,0,0);

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
            var publish_date = $('#edit_publish_date').val();
            var expired_date = $('#edit_expired_date').val();
            
            var today = new Date();
            today.setHours(0,0,0,0);
            
            var expDate = new Date(expired_date);
            expDate.setHours(0,0,0,0);

            // Validasi Expired Date Minimal Hari Ini
            if (!expired_date) {
                Swal.fire("Information", "Expired date is required!", "warning");
                return;
            }

            if (expDate < today) {
                Swal.fire("Information", "Expired date cannot be earlier than today!", "warning");
                return;
            }

            if (new Date(publish_date) > expDate) {
                Swal.fire("Information", "Publish date cannot be later than Expired date!", "warning");
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
                        Swal.fire("Information", event.message || "Dates updated successfully!", "success");
                        $('#modalxl').modal('hide');
                        tblpublished.ajax.reload(null, false);
                    } else {
                        Swal.fire("Information", event.message || "Failed to update dates", "error");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    block(false, '#modalbodyxl');
                    Swal.fire("Information", textStatus + ' : ' + errorThrown, "warning");
                }
            });
        });

    });
    
    // FUNCTION: DELETE AJAX
    function DeleteSurvey(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if (result.value == true) {
                block(true, '.nk-content-body');
                $.ajax({
                    url : "{{ url('/admin/usersurvey/delete') }}",
                    type: "POST",
                    data: { id: id, _token: '{{ csrf_token() }}' },
                    dataType: "json",
                    success: function(event) {
                        block(false, '.nk-content-body');
                        if (event.status == 'OK') {
                            Swal.fire("Information", event.message, "success");
                            tbldraft.ajax.reload(null, false); 
                            tblpublished.ajax.reload(null, false); 
                        } else {
                            Swal.fire("Information", event.message, "error");
                        }
                    },                    
                    error: function(jqXHR, textStatus, errorThrown){        
                        block(false, '.nk-content-body');
                        Swal.fire("Information", textStatus + ' : ' + errorThrown, "warning");
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