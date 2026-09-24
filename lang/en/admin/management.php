<?php

// Admin: Graph Management (admin/management).
return [
    'title'             => 'Graph Management',
    'desc'              => 'Aging AP / AR, revenue and expense overview.',
    'aging_ap_graphic'  => 'Aging AP Graphic',
    'aging_ar_graphic'  => 'Aging AR Graphic',
    'revenue'           => 'Revenue',
    'expense'           => 'Expense',

    // teks di dalam grafik (dipakai JS)
    'chart' => [
        'ar_profile'      => 'AR Aging Profile (IDRbn)',
        'ap_profile'      => 'AP Aging Profile (IDRbn)',
        'revenue_year'    => 'REVENUE :year',
        'expense_year'    => 'EXPENSE :year',
        'revenue_actual'  => 'TOTAL REVENUE ACTUAL',
        'revenue_budget'  => 'TOTAL REVENUE BUDGET',
        'expense_actual'  => 'TOTAL EXPENSE ACTUAL',
        'expense_budget'  => 'TOTAL EXPENSE BUDGET',
        'months'          => ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
    ],
];
