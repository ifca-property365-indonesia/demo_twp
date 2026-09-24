@extends('tenant.template.base')
@section('title', __('tenant/invoice.outstanding_title'))
@section('content')
<div class="page-body">
        <div class="page-head">
            <div class="page-head-row">
                <div class="page-head-content">
                    <h3 class="page-title">{{ __('tenant/invoice.outstanding_title') }}</h3>
                </div><!-- .page-head-content -->
            </div><!-- .page-head-row -->
        </div><!-- .page-head -->
        <div class="page-block">
            <div class="card">
                <div class="card-body">

                    {{-- sama dengan Proforma: tenant yang login (session tenant_df); mode semua tenant tanpa label --}}
                    @unless (App\Support\TenantScope::all())
                        <div id="tenantNoLabel"><span class="badge badge-soft-primary fs-6">{{ __('tenant/invoice.tenant_no', ['no' => $tenant_no]) }}</span></div>
                    @endunless
                    <div class="table-responsive">
                        <?php
                            if(!empty($list_bill)) {
                        ?>
                        <table id="tblBilling" class="table table-bordered table-striped" role="grid" aria-describedby="tblBilling_info">
                            <thead class="table-dark">
                                <tr role='row'>
                                    <th class="sorting text-center" style="width: 7px; vertical-align: middle;">{{ __('tenant/invoice.col_no') }}</th>
                                    <th class="sorting text-center" style="width: 24px;">{{ __('tenant/invoice.document_number') }}</th>
                                    <th class="sorting text-center" style="width: 100px;">{{ __('tenant/invoice.doc_date') }}</th>
                                    <th class="sorting text-center" style="width: 100px;">{{ __('tenant/invoice.due_date') }}</th>
                                    <th class="sorting text-center" style="vertical-align: middle;">{{ __('tenant/invoice.description') }}</th>
                                    <th class="sorting text-center" style="width: 110px; vertical-align: middle;">{{ __('tenant/invoice.period') }}</th>
                                    <th class="sorting text-center" style="vertical-align: middle;">{{ __('tenant/invoice.outstanding') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    if (!empty($list_bill))
                                    { 
                                        echo $list_bill;
                                    }  
                                ?>
                            </tbody>
                        </table>
                        <?php  
                            } else {
                                echo "<div class='text-center py-5 text-body-secondary'><i class='cil-wallet fs-1 d-block mb-2'></i>" . e(__('common.no_data')) . "</div>";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div><!-- .page-block -->
    </div>
    <script type="text/javascript">
        var totalOutstanding = {{ $totalOutstanding ?? 0 }};

        $(document).ready(function () {

            $('#tblBilling').DataTable({
                paging: false,
                searching: true,
                ordering: true,
                info: false,
                lengthChange: false,
                dom: 'Bfrtip',
                // label Tenant No satu baris dengan kotak Search (kiri - kanan)
                initComplete: function () {
                    var $search = $('#tblBilling_wrapper .dt-search');
                    var $row = $('<div class="d-flex flex-wrap align-items-center gap-2 mb-2"></div>');
                    $search.before($row);
                    $row.append($('#tenantNoLabel'), $search.addClass('ms-auto mb-0'));
                },

                // PDF: tampilan, orientasi & buka di tab baru diatur assets/app/js/pdf-export.js
                buttons: [{
                    extend: 'pdfHtml5',
                    title: @json(__('tenant/invoice.pdf_title')),
                    className: 'btn btn-primary mb-2',
                    text: '<i class="cil-cloud-download"></i>&nbsp;' + @json(__('common.generate_pdf')),
                    init: function(api, node){
                        $(node).removeClass('dt-button');
                    }
                }]
            });

        });
        </script>
@endsection
