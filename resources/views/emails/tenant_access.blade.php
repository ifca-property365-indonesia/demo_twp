{{-- Email akses portal tenant (App\Mail\TenantAccessMail). HTML sederhana dengan style inline
     supaya tampil sama di Gmail / Outlook. --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carstensz Tenant Web Portal</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f7; font-family:Arial, Helvetica, sans-serif; color:#212631;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f7; padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background:#ffffff; border-radius:8px; overflow:hidden; border:1px solid #e3e5ea;">
                <tr>
                    <td style="background:#1d2333; padding:20px 28px;">
                        <img src="{{ $logoUrl }}" alt="Carstensz" height="44" style="display:block; height:44px; border:0;">
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px;">
                        <p style="margin:0 0 16px; font-size:15px;">Yth. <strong>{{ $name }}</strong>,</p>

                        @if ($reason === 'email_changed')
                            <p style="margin:0 0 16px; font-size:14px; line-height:1.6;">
                                Email akun Anda di <strong>Carstensz Tenant Web Portal</strong> telah diperbarui oleh Pengelola Gedung
                                dan kata sandinya telah diatur ulang. Akun Anda sudah dapat diakses dengan data berikut:
                            </p>
                        @else
                            <p style="margin:0 0 16px; font-size:14px; line-height:1.6;">
                                Akun Anda di <strong>Carstensz Tenant Web Portal</strong> telah dibuat dan sudah dapat diakses
                                dengan data berikut:
                            </p>
                        @endif

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f6f8; border:1px solid #e3e5ea; border-radius:6px; margin:0 0 20px;">
                            <tr>
                                <td style="padding:12px 16px 4px; font-size:12px; color:#6b7280; width:110px;">Alamat / URL</td>
                                <td style="padding:12px 16px 4px; font-size:14px;"><a href="{{ $portalUrl }}" style="color:#4f5bd5;">{{ $portalUrl }}</a></td>
                            </tr>
                            <tr>
                                <td style="padding:4px 16px; font-size:12px; color:#6b7280;">Email</td>
                                <td style="padding:4px 16px; font-size:14px;"><strong>{{ $email }}</strong></td>
                            </tr>
                            <tr>
                                <td style="padding:4px 16px 12px; font-size:12px; color:#6b7280;">Kata Sandi</td>
                                <td style="padding:4px 16px 12px; font-size:14px; font-family:Consolas, 'Courier New', monospace;"><strong>{{ $password }}</strong></td>
                            </tr>
                        </table>

                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
                            <tr>
                                <td style="background:#4f5bd5; border-radius:6px;">
                                    <a href="{{ $portalUrl }}" style="display:inline-block; padding:10px 22px; color:#ffffff; font-size:14px; font-weight:bold; text-decoration:none;">Masuk ke Portal</a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0 0 24px; font-size:13px; line-height:1.6; color:#9a6a00; background:#fff5e0; padding:10px 14px; border-radius:6px;">
                            Demi keamanan, segera ganti kata sandi Anda setelah masuk melalui menu <strong>Lihat Profil &rarr; Kata Sandi</strong>,
                            dan jangan bagikan email ini kepada orang lain.
                        </p>

                        <hr style="border:0; border-top:1px solid #e3e5ea; margin:0 0 20px;">

                        <p style="margin:0 0 12px; font-size:13px; line-height:1.6; color:#4b5563;">
                            <em>
                                @if ($reason === 'email_changed')
                                    Your Carstensz Tenant Web Portal account email has been updated by Building Management and its
                                    password has been reset.
                                @else
                                    Your Carstensz Tenant Web Portal account has been created.
                                @endif
                                You can now log in at the URL above with the email and password shown. For your security, please
                                change your password after logging in (View Profile &rarr; Password) and do not share this email.
                            </em>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="background:#f5f6f8; padding:14px 28px; font-size:11px; color:#9ca3af; line-height:1.5;">
                        Email ini dikirim otomatis oleh Carstensz Tenant Web Portal. Mohon tidak membalas email ini.<br>
                        This is an automated email from Carstensz Tenant Web Portal. Please do not reply.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
