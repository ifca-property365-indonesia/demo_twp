<?php

// Teks bawaan plugin JS (DataTables, Select2, jQuery Validate), dipasang sekali di layouts/app.
return [
    'datatables' => [
        'search'        => 'Search:',
        'lengthMenu'    => 'Show _MENU_ entries',
        'info'          => 'Showing _START_ to _END_ of _TOTAL_ entries',
        'infoEmpty'     => 'Showing 0 to 0 of 0 entries',
        'infoFiltered'  => '(filtered from _MAX_ total entries)',
        'loadingRecords'=> 'Loading...',
        'processing'    => 'Processing...',
        'zeroRecords'   => 'No matching records found',
        'emptyTable'    => 'No data available in table',
        'first'         => 'First',
        'last'          => 'Last',
        'next'          => 'Next',
        'previous'      => 'Previous',
    ],
    'select2' => [
        'noResults' => 'No results found',
        'searching' => 'Searching...',
    ],
    'validate' => [
        'required' => 'This field is required.',
        'email'    => 'Please enter a valid email address.',
        'number'   => 'Please enter a valid number.',
        'date'     => 'Please enter a valid date.',
        'equalTo'  => 'Please enter the same value again.',
        'maxlength'=> 'Please enter no more than {0} characters.',
        'minlength'=> 'Please enter at least {0} characters.',
    ],
];
