@extends('tenant.template.base')
@section('content')
<div class="page-body">
        <div class="page-head">
            <div class="page-head-row">
                <div class="page-head-content">
                    <h3 class="page-title">{{ __('tenant/history.invoice_title') }}</h3>
                </div><!-- .page-head-content -->
            </div><!-- .page-head-row -->
        </div><!-- .page-head -->
        <div class="page-block">
            <div class="card mb-3">
                <div class="card-body">
                    <form id="form_search" method="POST" action="" novalidate>
                        <div class="row g-3 align-items-end">
                            <div class="col-sm-6 col-lg-3">
                                <label for="start_date" class="form-label">{{ __('tenant/history.start_date_doc') }}</label>
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                    <input type="text" id="start_date" name="start_date" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="{{ date('d/m/Y', strtotime('-3 months', strtotime(date('Y-m-01')))) }}" required autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <label for="end_date" class="form-label">{{ __('tenant/history.end_date_doc') }}</label>
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                                    <input type="text" id="end_date" name="end_date" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="{{ date('d/m/Y') }}" required autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-4 col-lg-2">
                                <button type="submit" id="btnSearch" class="btn btn-primary w-100"><i class="cil-search"></i><span>{{ __('common.search') }}</span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblBilling" class="table table-bordered table-striped" role="grid" aria-describedby="tblBilling_info">
                            <thead class="table-dark">
                                <tr role='row'>
                                    <th class="sorting text-center" style="width: 7px; vertical-align: middle;">{{ __('tenant/history.col_no') }}</th>
                                    <th class="sorting text-center" style="width: 24px;">{{ __('tenant/history.document_number') }}</th>
                                    <th class="sorting text-center" style="width: 100px;">{{ __('tenant/history.doc_date') }}</th>
                                    <th class="sorting text-center" style="width: 100px;">{{ __('tenant/history.due_date') }}</th>
                                    <th class="sorting text-center" style="vertical-align: middle;">{{ __('tenant/history.description') }}</th>
                                    <th class="sorting text-center" style="width: 110px; vertical-align: middle;">{{ __('tenant/history.period') }}</th>
                                    <th class="sorting text-center" style="vertical-align: middle;">{{ __('tenant/history.outstanding') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- .page-block -->
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
                    className: 'text-end',
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

        $('#form_search').on('submit', function (e) {
            e.preventDefault();
            $('#tblBilling').DataTable().ajax.reload();
        });

        
        </script>
@endsection