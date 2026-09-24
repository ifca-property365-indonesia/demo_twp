<?php

// Form ticket tenant (resources/views/tenant/ticket) + pesan dari Tenant\TicketController.
return [
    'title'                 => 'Ticket',
    'new_ticket'            => 'New Ticket',
    'edit_ticket'           => 'Edit Ticket',
    'page_desc'             => 'Submit a request or complaint to building management.',
    'ticket_history'        => 'Ticket History',

    // field form
    'ticket_type'           => 'Ticket Type',
    'choose_ticket_type'    => 'Choose a Ticket Type',
    'type_request'          => 'Request',
    'type_complain'         => 'Complain',
    'choose_tenant'         => 'Choose a Tenant',
    'choose_unit'           => 'Choose a Unit',
    'ticket_number'         => 'Ticket Number',
    'location'              => 'Location',
    'requested_by'          => 'Requested By',
    'contact_no'            => 'Contact No',
    'choose_category'       => 'Choose a Category',
    'description_placeholder' => 'Describe the request / complaint',
    'picture_note'          => 'Max 2 MB. JPG, JPEG, PNG or GIF.',
    'picture_hint'          => 'Picture is uploaded only when you click Submit.',
    'current_picture'       => 'Current picture',
    'no_lot'                => 'No lot available',

    // validasi form
    'validation' => [
        'ticket_type' => 'Please select a type',
        'tenant_no'   => 'Please select a tenant',
        'lot_no'      => 'Please select a lot',
        'location'    => 'Please select a location',
        'req_by'      => 'Please select a req_by',
        'contact_no'  => 'Please select a contact_no',
        'category'    => 'Please select a category',
        'description' => 'Please select a description',
    ],

    // foto & reset
    'only_image'            => 'Only JPG, JPEG, PNG or GIF files are allowed.',
    'max_size'              => 'Maximum file size is 2 MB.',
    'picture_upload_failed' => 'Picture upload failed.',
    'picture_upload_failed_detail' => 'Picture upload failed: :status :error',
    'reset_title'           => 'Reset the form?',
    'reset_text'            => 'All entered data and the selected picture will be cleared.',
    'reset_confirm'         => 'Yes, reset',
    'save_error'            => ':status Save : :error',

    // daftar harga
    'item_price_list'       => 'Item Price List',
    'service_price_list'    => 'Service Price List',
    'col_no'                => 'No',
    'col_code'              => 'Code',
    'col_price'             => 'Price',

    // pesan error dari controller
    'tenant_not_found'      => 'Tenant not found: :tenant',
    'doc_control_not_found' => 'Document control not found for :entity / :prefix',
    'doc_format_not_found'  => 'Document format not found (rowId=:row, type_format=:format)',

    // status ticket (kode sv_entry_*) versi label tenant
    'statuses' => [
        'R' => 'Submit',
        'O' => 'Open',
        'A' => 'Accepted',
        'S' => 'Survey',
        'P' => 'Process',
        'F' => 'Confirm',
        'M' => 'Modify',
        'Z' => 'Charged Approved',
        'Y' => 'Approve',
        'C' => 'Close',
        'X' => 'Cancel',
    ],
];
