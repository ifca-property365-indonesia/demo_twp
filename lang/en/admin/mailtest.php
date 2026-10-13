<?php

return [
    'title'             => 'Test Email',
    'desc'              => 'Send a test email to check the SMTP settings (MAIL_* in .env).',
    'email'             => 'Email Address',
    'email_placeholder' => 'name@domain.com',
    'send'              => 'Send',
    'invalid_email'     => 'Please enter a valid email address.',
    'failed'            => 'Email failed to send',
    'sent'              => 'Test email sent to :email.',
    'smtp_info'         => 'SMTP: :host port :port, sender :from',
    'subject'           => 'SMTP Test :app',
    'body'              => "This is a test email to confirm that SMTP works.\n\nSent from :app (:url) on :time.",
];
