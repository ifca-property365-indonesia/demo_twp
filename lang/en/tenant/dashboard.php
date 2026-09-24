<?php

// Dasbor tenant (resources/views/tenant/dash) + PDF grafik pemakaian listrik (tenant/export/elchart).
return [
    'title'                  => 'Dashboard',

    // grafik utilitas
    'monthly_utility_usage'  => 'Monthly Utility Usage',
    'monthly_electric_usage' => 'Monthly Electric Usage',
    'monthly_water_usage'    => 'Monthly Water Usage',
    'monthly_gas_usage'      => 'Monthly Gas Usage',
    'monthly_usage'          => 'Monthly Usage',
    'usage'                  => 'Usage',
    'select_utility'         => '-- Select Utility --',
    'electric'               => 'Electric',
    'water'                  => 'Water',
    'gas'                    => 'Gas',
    'select_meter_id'        => '-- Select Meter ID --',
    'tab_area'               => 'Area',
    'tab_bar'                => 'Bar',
    'pdf_failed'             => 'Failed generating pdf file.',
    'months' => [
        1  => 'Jan',
        2  => 'Feb',
        3  => 'Mar',
        4  => 'Apr',
        5  => 'May',
        6  => 'Jun',
        7  => 'Jul',
        8  => 'Aug',
        9  => 'Sep',
        10 => 'Oct',
        11 => 'Nov',
        12 => 'Dec',
    ],

    // kartu notifikasi
    'important_notification' => 'Important Notification',
    'you_have_proforma'      => 'You have Proforma',
    'proforma_notification'  => 'Proforma Notification',
    'no_proforma'            => 'No Proforma',
    'invoice_notification'   => 'Invoice Notification',
    'you_have_invoice'       => 'You have Invoice',
    'no_invoice'             => 'No Invoice',

    // tabel ticket terbaru
    'latest_ticket'          => 'Our Latest Ticket',
    'new_ticket'             => 'New Ticket',
    'col_ticket_number'      => 'Ticket Number',
    'col_reported_date'      => 'Reported Date',
    'col_request_by'         => 'Request By',
    'col_ticket_type'        => 'Ticket Type',
    'col_ticket_status'      => 'Ticket Status',
    'no_ticket'              => 'No ticket yet.',
    'rechargeable'           => 'Rechargeable',
    'non_rechargeable'       => 'Non-Rechargeable',

    // overtime
    'cancel_overtime_confirm'=> 'Cancel this Request Overtime?',
    'ot_statuses' => [
        'process'   => 'Process',
        'waiting'   => 'Waiting to be activated',
        'activated' => 'Activated',
        'ended'     => 'Ended',
        'scheduled' => 'Scheduled',
        'canceled'  => 'Canceled',
        'closed'    => 'Closed',
    ],

    // PDF pemakaian listrik
    'electricity_usage'      => 'Electricity Usage',
    'generated_at'           => 'Generated :date',
    'col_lwbp_usage'         => 'LWBP Usage (kWh)',
    'col_wbp_usage'          => 'WBP Usage (kWh)',
];
