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
                                <span class="mr-2">Invoice Outstanding</span>
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <?php
                            if(!empty($list_bill)) {
                        ?>
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
                                echo "<p class='card-text badge badge-gray'>Data Not Available</p>";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block -->
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

                buttons: [{
                    extend: 'pdfHtml5',
                    title: 'Invoice',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    className: 'btn btn-primary mb-2',
                    text: '<em class="icon ni ni-download"></em>&nbsp;Generate PDF',

                    exportOptions: {
                        columns: ':visible'
                    },

                    customize: function (doc) {

                        // Margin
                        doc.pageMargins = [20,20,20,20];

                        // Style Header
                        doc.styles.tableHeader = {
                            bold: true,
                            fontSize: 10,
                            alignment: 'center',
                            fillColor: '#101924',
                            color: '#ffffff'
                        };

                        doc.styles.title = {
                            fontSize: 16,
                            bold: true,
                            alignment: 'center'
                        };

                        // Cari object table
                        var table = doc.content.find(function(item){
                            return item.table;
                        });

                        // Border
                        table.layout = {
                            hLineWidth: function () { return 0.8; },
                            vLineWidth: function () { return 0.8; },
                            hLineColor: function () { return '#000'; },
                            vLineColor: function () { return '#000'; },
                            paddingLeft: function () { return 5; },
                            paddingRight: function () { return 5; },
                            paddingTop: function () { return 4; },
                            paddingBottom: function () { return 4; }
                        };

                        // Lebar kolom
                        table.table.widths = [
                            '5%',   // No
                            '20%',  // Document Number
                            '12%',  // Doc Date
                            '12%',  // Due Date
                            '*',    // Description
                            '12%',  // Periode
                            '15%'   // Outstanding
                        ];

                        var body = table.table.body;

                        // Alignment isi tabel
                        for (var i = 1; i < body.length; i++) {

                            body[i][0].alignment = 'center';
                            body[i][1].alignment = 'center';
                            body[i][2].alignment = 'center';
                            body[i][3].alignment = 'center';
                            body[i][4].alignment = 'left';
                            body[i][5].alignment = 'center';
                            body[i][6].alignment = 'right';
                        }

                    },

                    init: function(api, node){
                        $(node).removeClass('dt-button');
                    }

                }]
            });

        });
        </script>
@endsection