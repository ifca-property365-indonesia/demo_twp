<?php

// Admin: Graph Management (admin/management).
return [
    'title'             => 'Grafik Manajemen',
    'desc'              => 'Ringkasan umur utang (AP) / piutang (AR), pendapatan, dan beban.',
    'aging_ap_graphic'  => 'Grafik Umur Utang (AP)',
    'aging_ar_graphic'  => 'Grafik Umur Piutang (AR)',
    'revenue'           => 'Pendapatan',
    'expense'           => 'Beban',

    // teks di dalam grafik (dipakai JS)
    'chart' => [
        'ar_profile'      => 'Profil Umur Piutang AR (IDR miliar)',
        'ap_profile'      => 'Profil Umur Utang AP (IDR miliar)',
        'revenue_year'    => 'PENDAPATAN :year',
        'expense_year'    => 'BEBAN :year',
        'revenue_actual'  => 'TOTAL PENDAPATAN AKTUAL',
        'revenue_budget'  => 'TOTAL ANGGARAN PENDAPATAN',
        'expense_actual'  => 'TOTAL BEBAN AKTUAL',
        'expense_budget'  => 'TOTAL ANGGARAN BEBAN',
        'months'          => ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'],
    ],
];
