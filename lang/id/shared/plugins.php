<?php

// Teks bawaan plugin JS (DataTables, Select2, jQuery Validate), dipasang sekali di layouts/app.
return [
    'datatables' => [
        'search'        => 'Cari:',
        'lengthMenu'    => 'Tampilkan _MENU_ data',
        'info'          => 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
        'infoEmpty'     => 'Menampilkan 0 sampai 0 dari 0 data',
        'infoFiltered'  => '(disaring dari total _MAX_ data)',
        'loadingRecords'=> 'Memuat...',
        'processing'    => 'Sedang diproses...',
        'zeroRecords'   => 'Tidak ada data yang cocok',
        'emptyTable'    => 'Tidak ada data',
        'first'         => 'Pertama',
        'last'          => 'Terakhir',
        'next'          => 'Berikutnya',
        'previous'      => 'Sebelumnya',
    ],
    'select2' => [
        'noResults' => 'Tidak ada hasil',
        'searching' => 'Mencari...',
    ],
    'validate' => [
        'required' => 'Kolom ini wajib diisi.',
        'email'    => 'Masukkan alamat email yang valid.',
        'number'   => 'Masukkan angka yang valid.',
        'date'     => 'Masukkan tanggal yang valid.',
        'equalTo'  => 'Masukkan nilai yang sama lagi.',
        'maxlength'=> 'Maksimal {0} karakter.',
        'minlength'=> 'Minimal {0} karakter.',
    ],
];
