<?php

// Dasbor tenant (resources/views/tenant/dash) + PDF grafik pemakaian listrik (tenant/export/elchart).
return [
    'title'                  => 'Dasbor',

    // grafik utilitas
    'monthly_utility_usage'  => 'Pemakaian Utilitas Bulanan',
    'monthly_electric_usage' => 'Pemakaian Listrik Bulanan',
    'monthly_water_usage'    => 'Pemakaian Air Bulanan',
    'monthly_gas_usage'      => 'Pemakaian Gas Bulanan',
    'monthly_usage'          => 'Pemakaian Bulanan',
    'usage'                  => 'Pemakaian',
    'select_utility'         => '-- Pilih Utilitas --',
    'electric'               => 'Listrik',
    'water'                  => 'Air',
    'gas'                    => 'Gas',
    'select_meter_id'        => '-- Pilih ID Meter --',
    'tab_area'               => 'Area',
    'tab_bar'                => 'Batang',
    'pdf_failed'             => 'Gagal membuat file PDF.',
    'months' => [
        1  => 'Jan',
        2  => 'Feb',
        3  => 'Mar',
        4  => 'Apr',
        5  => 'Mei',
        6  => 'Jun',
        7  => 'Jul',
        8  => 'Agu',
        9  => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des',
    ],

    // kartu notifikasi
    'important_notification' => 'Pemberitahuan Penting',
    'you_have_proforma'      => 'Anda memiliki Proforma',
    'proforma_notification'  => 'Pemberitahuan Proforma',
    'no_proforma'            => 'Tidak Ada Proforma',
    'invoice_notification'   => 'Pemberitahuan Tagihan',
    'you_have_invoice'       => 'Anda memiliki Tagihan',
    'no_invoice'             => 'Tidak Ada Tagihan',

    // tabel ticket terbaru
    'latest_ticket'          => 'Tiket Terbaru Kami',
    'new_ticket'             => 'Tiket Baru',
    'col_ticket_number'      => 'Nomor Tiket',
    'col_wo_number'          => 'No. WO',
    'col_reported_date'      => 'Tanggal Lapor',
    'col_request_by'         => 'Diminta Oleh',
    'col_ticket_type'        => 'Jenis Tiket',
    'col_ticket_status'      => 'Status Tiket',
    'no_ticket'              => 'Belum ada tiket.',
    'rechargeable'           => 'Dapat Ditagihkan',
    'non_rechargeable'       => 'Tidak Ditagihkan',

    // overtime
    'cancel_overtime_confirm'=> 'Batalkan Permintaan Lembur ini?',
    'ot_statuses' => [
        'process'   => 'Diproses',
        'waiting'   => 'Menunggu diaktifkan',
        'activated' => 'Aktif',
        'ended'     => 'Berakhir',
        'scheduled' => 'Terjadwal',
        'canceled'  => 'Dibatalkan',
        'closed'    => 'Ditutup',
    ],

    // PDF pemakaian listrik
    'electricity_usage'      => 'Pemakaian Listrik',
    'generated_at'           => 'Dibuat :date',
    'col_lwbp_usage'         => 'Pemakaian LWBP (kWh)',
    'col_wbp_usage'          => 'Pemakaian WBP (kWh)',
];
