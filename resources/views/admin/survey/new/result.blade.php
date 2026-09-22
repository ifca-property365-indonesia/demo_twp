@extends('admin.template.layout2.base')
@section('content')
<div class="page-body">
    <div class="page-head">
        <div class="page-head-row">
            <div class="page-head-content">
                <h3 class="page-title">Survey Results</h3>
            </div>
        </div>
    </div>

    <div class="page-block">
        @if(count($surveys) > 0)
            @foreach($surveys as $survey)
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="survey-title text-primary mb-1">{{ $survey->title }}</h4>
                        <div class="sub-text mb-3">
                            Total Respondents: <strong class="text-dark">{{ $survey->totalRespondents }}</strong> People &nbsp;|&nbsp; 
                            Publish Date: <strong>{{ date('d-m-Y', strtotime($survey->publish_date)) }}</strong>
                        </div>

                        @if(count($survey->questions) > 0)
                            @foreach($survey->questions as $index => $q)
                                <div class="mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                                    <h6 class="text-dark">{{ $index + 1 }}. {{ $q->question_text }}</h6>
                                    
                                    @if($q->question_type == 'multiple_choice')
                                        <!-- MULTIPLE CHOICE VIEW (PERCENTAGE) -->
                                        <div class="gy-3 mt-2">
                                            @foreach($q->options as $opt)
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-sm-3 col-12 mb-1 mb-sm-0">
                                                        <span class="sub-text fw-bold">{{ $opt->option_text }}</span>
                                                    </div>
                                                    <div class="col-sm-7 col-9">
                                                        <div class="progress bg-light" style="height: 1.2rem; border-radius: 4px; position: relative;">
                                                            <div class="progress-bar bg-primary" 
                                                                 role="progressbar" 
                                                                 style="width: {{ $opt->percentage }}%;" 
                                                                 aria-valuenow="{{ $opt->percentage }}" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="100">
                                                                @if($opt->percentage > 5) 
                                                                    {{ $opt->percentage }}% 
                                                                @endif
                                                            </div>
                                                            
                                                            <!-- POSISI PERSENTASE <= 5% (TERMASUK 0%) DI TENGAH BAR ABU-ABU -->
                                                            @if($opt->percentage <= 5)
                                                                <div style="position: absolute; width: 100%; text-align: center; font-size: 11px; font-weight: bold; color: #526484; line-height: 1.2rem; pointer-events: none;">
                                                                    {{ $opt->percentage }}%
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2 col-3 text-end">
                                                        <!-- VOTE COUNT BADGE -->
                                                        <span class="badge badge-soft-secondary btn-show-voters" 
                                                            style="cursor: pointer;" 
                                                            data-optid="{{ $opt->id }}" 
                                                            data-opttext="{{ $opt->option_text }}" 
                                                            data-surveytitle="{{ $survey->title }}" 
                                                            title="Click to view voter list">
                                                            <i class="cil-people"></i> {{ $opt->count }} Votes
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <!-- QUESTIONNAIRE VIEW (ANSWER LIST WITH DATATABLE PAGINATION) -->
                                        <div class="table-responsive mt-3">
                                            <table class="table table-striped table-bordered table-sm mb-0 tbl-essay-answers" width="100%">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="25%">Respondent</th>
                                                        <th>Questionnaire Answer</th>
                                                        <th width="20%">Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($q->answers as $ans)
                                                        <tr>
                                                            <td class="fw-bold" style="font-size: 12px;">{{ $ans->email }}</td>
                                                            <td style="white-space: pre-wrap; font-size: 13px;">{{ $ans->essay_answer }}</td>
                                                            <td style="font-size: 12px;">{{ date('d M Y, H:i', strtotime($ans->created_at)) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted py-3">No responses from respondents yet.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center py-2 mb-0">No questions in this survey yet.</p>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Main Survey Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $surveys->links('pagination::bootstrap-4') }}
            </div>

        @else
            <div class="card">
                <div class="card-body">
                    <p class="card-text badge badge-soft-secondary mb-0">No Survey Available</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- SCRIPT TO SHOW VOTER LIST MODAL & INIT DATATABLE -->
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // 1. INIT DATATABLE FOR ESSAY TABLE
        $('.tbl-essay-answers').each(function() {
            var rowCount = $(this).find('tbody tr').length;
            var isEmpty = $(this).find('tbody tr td[colspan="3"]').length > 0;

            if (!isEmpty && rowCount > 0) {
                $(this).DataTable({
                    "pageLength": 10,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "language": {
                        "paginate": {
                            "previous": "<i class='cil-chevron-left'></i>",
                            "next": "<i class='cil-chevron-right'></i>"
                        },
                        "zeroRecords": "No answers found",
                        "info": "Showing _START_ - _END_ of _TOTAL_ answers",
                        "infoEmpty": "Showing 0 answers"
                    }
                });
            }
        });

        // 2. MODAL SCRIPT FOR MULTIPLE CHOICE VOTERS
        $(document).on('click', '.btn-show-voters', function() {
            var optionId = $(this).data('optid');
            var optionText = $(this).data('opttext');
            var surveyTitle = $(this).data('surveytitle');

            $('#modaltitlexl').html('Voter List for Option: <span class="text-primary">' + optionText + '</span> in survey <span class="text-primary">' + surveyTitle + '</span>');
            
            $('#modalbodyxl').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading respondent list...</p></div>');
            $('.modal-footer').html('<button type="button" class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">Close</button>');
            
            $('#modalxl').modal({backdrop: 'static', keyboard: false});
            $('#modalxl').modal('show');

            $.ajax({
                url: "{{ url('/admin/usersurvey/voters') }}",
                type: "POST",
                data: { option_id: optionId },
                dataType: "json",
                success: function(response) {
                    console.log("AJAX Success Response:", response); // <-- Bantuan Debugging
                    
                    if (response && response.status === 'OK') {
                        var html = '<div class="table-responsive"><table class="table table-bordered table-striped table-sm" id="tblModalVoters" width="100%">';
                        html += '<thead class="table-light"><tr><th width="8%">No.</th><th width="25%">Respondent Email</th><th>Remarks</th><th width="22%">Voting Time</th></tr></thead><tbody>';

                        // Cek dengan aman apakah data ada
                        var hasData = response.data && response.data.length > 0;

                        if (hasData) {
                            $.each(response.data, function(index, val) {
                                // JIKA REMARKS KOSONG, TAMPILKAN "-" DENGAN AMAN
                                var remarksText = (val.remarks && val.remarks !== "null") ? val.remarks : '<em class="text-muted">-</em>';

                                html += '<tr>';
                                html += '<td class="text-center">' + (index + 1) + '</td>';
                                html += '<td class="fw-bold" style="font-size: 13px;">' + (val.email || '-') + '</td>';
                                html += '<td style="font-size: 13px;">' + remarksText + '</td>';
                                html += '<td style="font-size: 12px;">' + (val.created_at || '-') + '</td>';
                                html += '</tr>';
                            });
                        } else {
                            html += '<tr><td colspan="4" class="text-center text-muted py-3">No voters found.</td></tr>';
                        }

                        html += '</tbody></table></div>';
                        $('#modalbodyxl').html(html); // Timpa status loading

                        if (hasData) {
                            $('#tblModalVoters').DataTable({
                                "pageLength": 10,
                                "lengthChange": false,
                                "searching": false,
                                "ordering": true,
                                "info": true,
                                "autoWidth": false,
                                "language": {
                                    "paginate": {
                                        "previous": "<i class='cil-chevron-left'></i>",
                                        "next": "<i class='cil-chevron-right'></i>"
                                    },
                                    "zeroRecords": "No voters found",
                                    "info": "Showing _START_ - _END_ of _TOTAL_ respondents",
                                    "infoEmpty": "Showing 0 respondents"
                                }
                            });
                        }
                    } else {
                        // Tampilkan pesan error dari backend ke dalam Modal
                        var errMsg = (response && response.message) ? response.message : 'Unknown error from server.';
                        $('#modalbodyxl').html('<div class="alert alert-danger mb-0"><strong>Server Error:</strong><br>' + errMsg + '</div>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText); // <-- Bantuan Debugging
                    $('#modalbodyxl').html('<div class="alert alert-danger mb-0"><strong>System Error:</strong> ' + errorThrown + '<br><small>Check console (F12) for details.</small></div>');
                }
            });
        });
    });
</script>
@endsection