@extends('tenant.template.base')

@section('title', __('tenant/billing.title'))

@section('content')
    <div class="page-body">
        <div class="page-head">
            <div class="page-head-row">
                <div class="page-head-content">
                    <h3 class="page-title">{{ __('tenant/billing.title') }}</h3>
                </div>
            </div>
        </div>
        <div class="page-block">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        @if (!empty($list_bill))
                            <table id="tblBilling" class="table table-bordered table-striped" role="grid" aria-describedby="tblBilling_info">
                                <thead class="table-dark">
                                    <tr role="row">
                                        <th class="text-center" style="width: 48px;">{{ __('common.col_no') }}</th>
                                        <th class="text-center">{{ __('tenant/billing.col_document_number') }}</th>
                                        <th class="text-center" style="width: 110px;">{{ __('tenant/billing.col_doc_date') }}</th>
                                        <th class="text-center" style="width: 110px;">{{ __('tenant/billing.col_due_date') }}</th>
                                        <th class="text-center">{{ __('common.description') }}</th>
                                        <th class="text-center" style="width: 110px;">{{ __('common.period') }}</th>
                                        <th class="text-center" style="width: 80px;">{{ __('common.currency') }}</th>
                                        <th class="text-center">{{ __('tenant/billing.col_outstanding') }}</th>
                                    </tr>
                                </thead>
                                <tbody>{!! $list_bill !!}</tbody>
                                <tfoot>{!! $footer_bill ?? '' !!}</tfoot>
                            </table>
                        @else
                            <div class="text-center py-5 text-body-secondary">
                                <i class="cil-wallet fs-1 d-block mb-2"></i>
                                {{ __('common.no_data') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(function () {
        if ($('#tblBilling').length) {
            $('#tblBilling').DataTable({
                paging: false,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'pdf',
                    title: @json(__('tenant/billing.title')),
                    className: 'btn btn-primary mb-2',
                    text: '<i class="cil-cloud-download"></i>&nbsp;' + @json(__('common.generate_pdf')),
                    init: function (api, node) { $(node).removeClass('dt-button'); }
                }]
            });
        }
    });
</script>
@endpush
