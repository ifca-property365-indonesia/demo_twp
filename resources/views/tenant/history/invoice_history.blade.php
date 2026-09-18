@extends('tenant.template.base')
@section('content')
<style type="text/css">
    .transbox {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 35%;
        background: rgba(0,0,0,0.5);
        padding-top: 50px;
        padding-left: 50px;
        padding-right:30px;
    }
    </style>
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Dashboard</h3>
                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div><!-- .nk-block-head -->
        <div class="nk-block">
            <div class="card card-bordered mt-3">
                <div class="card-inner">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title">
                                <span class="mr-2">Invoice</span>
                            </h6>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-sm-2">
                            <label for="start" class=""> Start Date (Doc Date)  </label>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left">
                                    <em class="icon ni ni-calendar"></em>
                                </div>
                                <input type="text" 
                                    id="start_date" 
                                    name="start_date" 
                                    class="form-control date-picker" 
                                    data-date-format="dd/mm/yyyy" 
                                    value="{{ date('d/m/Y', strtotime('-3 months', strtotime(date('Y-m-01')))) }}" 
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-2">
                            <label for="end" class="control-label"> End Date (Doc Date)  </label>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left">
                                    <em class="icon ni ni-calendar"></em>
                                </div>
                                <input type="text"
                                    id="end_date"
                                    name="end_date"
                                    class="form-control date-picker"
                                    data-date-format="dd/mm/yyyy"
                                    value="{{ date('d/m/Y') }}"
                                    required
                                >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" id="btnSearch" class="btn btn-primary">
                                <em class="icon ni ni-search"></em>
                                <span>Search</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive mt-3">
                        <table id="tblBilling" class="table table-bordered table-striped" role="grid" aria-describedby="tblBilling_info">
                            <thead style="background:#101924; color: #ffffff;">
                                <tr role='row'>
                                    <th class="sorting text-center" style="width: 7px; vertical-align: middle;">No.</th>
                                    <th class="sorting text-center" style="width: 24px;">Document Number</th>
                                    <th class="sorting text-center" style="width: 100px;">Doc Date</th>
                                    <th class="sorting text-center" style="width: 100px;">Due Date</th>
                                    <th class="sorting text-center" style="vertical-align: middle;">Description</th>
                                    <th class="sorting text-center" style="width: 110px; vertical-align: middle;">Periode</th>
                                    <th class="sorting text-center" style="vertical-align: middle;">Outstanding</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block -->
    </div>
    <script type="text/javascript">
        $('#tblBilling').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 20,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            paging: true,

            ajax: {
                url: "{{ url('/tenant/hinvoiceTable') }}",
                data: function (d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },

            columns: [
                {
                    data: null,
                    name: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'doc_no',
                    name: 'doc_no'
                },
                {
                    data: 'doc_date',
                    name: 'doc_date',
                    render: function (data) {
                        if (!data) return '';

                        return new Date(data).toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric'
                        });
                    }
                },
                {
                    data: 'void_date',
                    name: 'void_date',
                    render: function (data) {
                        if (!data) return '';

                        return new Date(data).toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric'
                        });
                    }
                },
                {
                    data: 'descs',
                    name: 'descs'
                },
                {
                    data: 'trx_date',
                    name: 'trx_date',
                    render: function (data) {
                        if (!data) return '';

                        return new Date(data).toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric'
                        });
                    }
                },
                {
                    data: 'fdoc_amt',
                    name: 'fdoc_amt',
                    className: 'text-right',
                    render: function (data) {
                        if (data == null) return '0.00';

                        return Number(data).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
            ],

            order: [[1, 'desc']]
        });

        $('#btnSearch').on('click', function () {
            $('#tblBilling').DataTable().ajax.reload();
        });

        $('.dataTables_filter').addClass('mb-3');
        
        </script>
@endsection