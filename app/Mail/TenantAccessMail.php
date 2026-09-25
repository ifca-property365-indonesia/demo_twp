<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email pemberitahuan akses portal tenant: URL portal, email login dan password.
 * Dikirim Admin\WsbangunController saat akun login tenant dibuat (business/insert|edit dari
 * IFCA) atau email akunnya diganti dari IFCA (password direset ke password default).
 *
 * $reason: 'created' | 'email_changed'
 */
class TenantAccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $reason = 'created',
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akses Carstensz Tenant Web Portal / Tenant Web Portal Access',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant_access',
            with: [
                // PORTAL_URL (.env), bukan APP_URL: email bisa dikirim dari server / lokal yang
                // APP_URL-nya berbeda dengan alamat portal yang dibuka tenant
                'portalUrl' => rtrim(config('app.portal_url'), '/') . '/',
                'logoUrl'   => rtrim(config('app.portal_url'), '/') . '/img/logoweb/carstensz-logo-new2.png',
            ],
        );
    }
}
