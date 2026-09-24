@extends('tenant.template.base')
@section('content')
<div class="page-body">
        <div class="page-head">
            <div class="page-head-row">
                <div class="page-head-content">
                    <h3 class="page-title">{{ __('tenant/invoice.proforma_title') }}</h3>
                </div><!-- .page-head-content -->
            </div><!-- .page-head-row -->
        </div><!-- .page-head -->
        <div class="page-block">
            <div class="card">
                <div class="card-body">

                    {{-- Mode semua tenant (akun admin di portal tenant): daftar berisi banyak tenant,
                         jadi label satu tenant tidak relevan. --}}
                    @unless (App\Support\TenantScope::all())
                        <div class="mb-3"><span class="badge badge-soft-primary fs-6">{{ __('tenant/invoice.tenant_no', ['no' => $tenant_no]) }}</span></div>
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
                                    <th class="sorting text-center" style="vertical-align: middle;">{{ __('tenant/invoice.check') }}</th>
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
                dom: "Bfrtip",
                buttons: [
                    {
                        extend: 'pdf',
                        title: @json(__('tenant/invoice.proforma_title')),
                        orientation: 'landscape',
                        pageSize: 'A4',
                        className: 'btn btn-primary mb-2',
                        text: '<i class="cil-cloud-download"></i>&nbsp;' + @json(__('common.generate_pdf')),

                        customize: function (doc) {

                            // Border tabel
                            doc.content[1].layout = {
                                hLineWidth: function () { return 0.8; },
                                vLineWidth: function () { return 0.8; },
                                hLineColor: function () { return '#000'; },
                                vLineColor: function () { return '#000'; },
                                paddingLeft: function () { return 5; },
                                paddingRight: function () { return 5; },
                                paddingTop: function () { return 4; },
                                paddingBottom: function () { return 4; }
                            };

                            // Style Header
                            doc.styles.tableHeader = {
                                bold: true,
                                fontSize: 10,
                                alignment: 'center',
                                fillColor: '#101924',
                                color: '#ffffff'
                            };

                            var body = doc.content[1].table.body;

                            // Alignment tiap kolom
                            for (var i = 1; i < body.length; i++) {

                                body[i][0].alignment = 'center';
                                body[i][1].alignment = 'center';
                                body[i][2].alignment = 'center';
                                body[i][3].alignment = 'center';
                                body[i][4].alignment = 'left';
                                body[i][5].alignment = 'center';
                                body[i][6].alignment = 'right';

                            }

                            // Lebar kolom
                            doc.content[1].table.widths = [
                                '5%',
                                '20%',
                                '12%',
                                '12%',
                                '*',
                                '12%',
                                '15%'
                            ];

                        },

                        init: function(api, node, config) {
                            $(node).removeClass('dt-button');
                        }
                    }
                ]
            });
        });
    </script>
@endsection