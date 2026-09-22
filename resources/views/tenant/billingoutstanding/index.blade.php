@extends('tenant.template.base')

@section('title', 'Billing Outstanding')

@section('content')
    <div class="page-body">
        <div class="page-head">
            <div class="page-head-row">
                <div class="page-head-content">
                    <h3 class="page-title">Billing Outstanding</h3>
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
                                        <th class="text-center" style="width: 48px;">No.</th>
                                        <th class="text-center">Document Number</th>
                                        <th class="text-center" style="width: 110px;">Doc Date</th>
                                        <th class="text-center" style="width: 110px;">Due Date</th>
                                        <th class="text-center">Description</th>
                                        <th class="text-center" style="width: 110px;">Periode</th>
                                        <th class="text-center" style="width: 80px;">Currency</th>
                                        <th class="text-center">Outstanding</th>
                                    </tr>
                                </thead>
                                <tbody>{!! $list_bill !!}</tbody>
                                <tfoot>{!! $footer_bill ?? '' !!}</tfoot>
                            </table>
                        @else
                            <div class="text-center py-5 text-body-secondary">
                                <i class="cil-wallet fs-1 d-block mb-2"></i>
                                Data not available.
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
                    title: 'Billing Outstanding',
                    className: 'btn btn-primary mb-2',
                    text: '<i class="cil-cloud-download"></i>&nbsp;Generate PDF',
                    init: function (api, node) { $(node).removeClass('dt-button'); }
                }]
            });
        }
    });
</script>
@endpush
