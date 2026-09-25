<?php

// Form ticket tenant (resources/views/tenant/ticket) + pesan dari Tenant\TicketController.
return [
    'title'                 => 'Tiket',
    'new_ticket'            => 'Tiket Baru',
    'edit_ticket'           => 'Ubah Tiket',
    'page_desc'             => 'Ajukan permintaan atau keluhan kepada Pengelola Gedung.',
    'ticket_history'        => 'Riwayat Tiket',

    // field form
    'ticket_type'           => 'Jenis Tiket',
    'choose_ticket_type'    => 'Pilih Jenis Tiket',
    'type_request'          => 'Permintaan',
    'type_complain'         => 'Keluhan',
    'choose_tenant'         => 'Pilih Tenant',
    'choose_unit'           => 'Pilih Unit',
    'ticket_number'         => 'Nomor Tiket',
    'location'              => 'Lokasi',
    'requested_by'          => 'Diminta Oleh',
    'contact_no'            => 'No. Kontak',
    'choose_category'       => 'Pilih Kategori',
    'description_placeholder' => 'Jelaskan permintaan / keluhan Anda',
    'picture_note'          => 'Maks. 2 MB. JPG, JPEG, PNG atau GIF.',
    'picture_hint'          => 'Gambar baru diunggah saat Anda menekan Kirim.',
    'current_picture'       => 'Gambar saat ini',
    'no_lot'                => 'Tidak ada lot tersedia',

    // validasi form
    'validation' => [
        'ticket_type' => 'Silakan pilih jenis tiket',
        'tenant_no'   => 'Silakan pilih tenant',
        'lot_no'      => 'Silakan pilih lot',
        'location'    => 'Silakan isi lokasi',
        'req_by'      => 'Silakan isi nama peminta',
        'contact_no'  => 'Silakan isi nomor kontak',
        'category'    => 'Silakan pilih kategori',
        'description' => 'Silakan isi deskripsi',
    ],

    // foto & reset
    'only_image'            => 'Hanya file JPG, JPEG, PNG atau GIF yang diperbolehkan.',
    'max_size'              => 'Ukuran file maksimal 2 MB.',
    'picture_upload_failed' => 'Gagal mengunggah gambar.',
    'picture_upload_failed_detail' => 'Gagal mengunggah gambar: :status :error',
    'reset_title'           => 'Reset formulir?',
    'reset_text'            => 'Semua data yang telah diisi dan gambar yang dipilih akan dihapus.',
    'reset_confirm'         => 'Ya, reset',
    'save_error'            => ':status Simpan : :error',

    // daftar harga
    'item_price_list'       => 'Daftar Harga Barang',
    'service_price_list'    => 'Daftar Harga Jasa',
    'col_no'                => 'No',
    'col_code'              => 'Kode',
    'col_price'             => 'Harga',

    // pesan error dari controller
    'tenant_not_found'      => 'Tenant tidak ditemukan: :tenant',
    'doc_control_not_found' => 'Kontrol dokumen tidak ditemukan untuk :entity / :prefix',
    'doc_format_not_found'  => 'Format dokumen tidak ditemukan (rowId=:row, type_format=:format)',

    // tombol tutup ticket (status F -> C)
    'close_button'        => 'Selesai',
    'close_title'         => 'Pekerjaan sudah selesai?',
    'close_confirm'       => 'Work order :report akan ditutup dengan status Selesai. Lanjutkan?',
    'closed'              => 'Work order :report ditutup dengan status Selesai.',
    'close_not_found'     => 'Work order tidak ditemukan.',
    'close_not_allowed'   => 'Work order ini tidak berstatus Konfirmasi, sehingga tidak bisa ditutup.',

    // status ticket (kode sv_entry_*) versi label tenant
    'statuses' => [
        'R' => 'Diajukan',
        'O' => 'Terbuka',
        'A' => 'Diterima',
        'S' => 'Survei',
        'P' => 'Diproses',
        'F' => 'Konfirmasi',
        'M' => 'Diubah',
        'Z' => 'Disetujui (Berbayar)',
        'Y' => 'Disetujui',
        'C' => 'Selesai',
        'X' => 'Dibatalkan',
    ],
];
