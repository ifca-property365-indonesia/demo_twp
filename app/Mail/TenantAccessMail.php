<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Email pemberitahuan akses portal tenant: URL portal, email login dan password, dengan
 * lampiran user guide (USER_GUIDE). Dikirim Admin\WsbangunController saat akun login tenant
 * dibuat (business/insert|edit dari IFCA) atau email akunnya diganti dari IFCA (password
 * direset ke password default).
 *
 * $reason: 'created' | 'email_changed'
 */
class TenantAccessMail extends Mailable
{
    use Queueable, SerializesModels;

    /** User guide yang dilampirkan, relatif ke root project. Tidak ada -> email tetap terkirim tanpa lampiran. */
    public const USER_GUIDE = 'public/lainnya/attachments/AddFile/UserGuide/USER GUIDE TENANT WEB PORTAL CARSTENSZ.pdf';

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
            bcc: $this->bccAddresses(),
        );
    }

    /** Alamat BCC dari config mail.bcc (MAIL_BCC_ENABLED / MAIL_BCC_ADDRESS di .env). */
    private function bccAddresses(): array
    {
        if (!config('mail.bcc.enabled')) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode(',', (string) config('mail.bcc.address'))),
            fn ($a) => filter_var($a, FILTER_VALIDATE_EMAIL) && strcasecmp($a, $this->email) !== 0
        ));
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

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        $path = $this->userGuidePath();
        if (!is_file($path)) {
            Log::warning('User guide tidak ditemukan, email akses tenant dikirim tanpa lampiran', ['path' => $path, 'to' => $this->email]);

            return [];
        }

        return [
            Attachment::fromPath($path)->as(basename($path))->withMime('application/pdf'),
        ];
    }

    protected function userGuidePath(): string
    {
        return base_path(self::USER_GUIDE);
    }
}
