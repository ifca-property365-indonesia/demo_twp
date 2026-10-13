<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Menu admin "Test Email": tes koneksi SMTP (MAIL_* di .env) dengan mengirim email teks singkat
 * ke alamat yang diisi admin. Hanya untuk admin yang login (route admin-auth), supaya tidak bisa
 * dipakai orang lain untuk mengirim email lewat akun SMTP ini.
 *
 *   GET  /admin/mail-test          halaman form
 *   POST /admin/mail-test  email   {"status":"OK","pesan":"..."} atau {"status":"Failed","pesan":"<error SMTP>"}
 */
class MailTestController extends Controller
{
    public function index()
    {
        return view('admin.mailtest.index', ['smtp' => $this->smtp()]);
    }

    public function send(Request $request)
    {
        $email = trim((string) $request->input('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['status' => 'Failed', 'pesan' => __('admin/mailtest.invalid_email')], 422);
        }

        // isi email selalu bahasa Indonesia (teks yang diminta), apa pun bahasa tampilan admin
        $app = config('app.name');
        $body = __('admin/mailtest.body', ['app' => $app, 'url' => url('/'), 'time' => date('d-m-Y H:i:s')], 'id');
        $subject = __('admin/mailtest.subject', ['app' => $app], 'id');

        try {
            Mail::raw($body, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['status' => 'Failed', 'pesan' => $e->getMessage()], 500);
        }

        Log::info('Email test SMTP terkirim', ['to' => $email] + $this->smtp());

        return response()->json(['status' => 'OK', 'pesan' => __('admin/mailtest.sent', ['email' => $email])]);
    }

    /** Pengaturan SMTP yang dipakai (tanpa username / password). */
    private function smtp()
    {
        return [
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'from' => config('mail.from.address'),
        ];
    }
}
