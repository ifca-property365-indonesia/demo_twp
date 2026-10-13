<?php

return [
    'title'             => 'Tes Email',
    'desc'              => 'Kirim email percobaan untuk mengecek pengaturan SMTP (MAIL_* di .env).',
    'email'             => 'Alamat Email',
    'email_placeholder' => 'nama@domain.com',
    'send'              => 'Kirim',
    'invalid_email'     => 'Masukkan alamat email yang valid.',
    'failed'            => 'Email gagal dikirim',
    'sent'              => 'Email test terkirim ke :email.',
    'smtp_info'         => 'SMTP: :host port :port, pengirim :from',
    'subject'           => 'Test SMTP :app',
    'body'              => "Ini email testing untuk test SMTP berhasil.\n\nDikirim dari :app (:url) pada :time.",
];
