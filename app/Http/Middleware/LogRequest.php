<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Catat setiap request yang ditangani controller (web & api) ke log controller-nya
 * (storage/logs/<yyyy-mm-dd>/<admin|tenant>/<controller>.log, lihat App\Logging\ControllerLog):
 *   POST admin/news/save -> Admin\NewsPromoController@save 200 45ms user=... ip=...
 * dengan input (password/token disamarkan, file hanya nama & ukuran).
 * Level: info; warning kalau status 4xx atau respons berisi tanda gagal
 * ("status":"Fail"/"Failed"/"error", "success":false, "Error":true, teks "Bad request"/"failed");
 * error kalau status 5xx. Exception-nya sendiri dicatat terpisah oleh exception handler.
 */
class LogRequest
{
    private const SECRET = '/pass|token|secret|^key$|api_key|signature/i';

    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        // Banyak controller memakai echo (mis. echo json_encode(...), echo 'Bad request') alih-alih
        // return. Output itu ditangkap lalu digabung ke isi respons (yang dikirim ke browser tetap
        // sama) supaya tanda gagalnya ikut terbaca di log.
        ob_start();
        $level = ob_get_level();
        try {
            $response = $next($request);
        } finally {
            $echoed = ob_get_level() === $level ? (string) ob_get_clean() : '';
        }
        if ($echoed !== '') {
            if ($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse
                || $response instanceof \Symfony\Component\HttpFoundation\StreamedResponse) {
                echo $echoed;
            } else {
                $response->setContent($echoed . $response->getContent());
            }
        }

        try {
            $this->write($request, $response, (int) round((microtime(true) - $start) * 1000));
        } catch (\Throwable $e) {
            // log tidak boleh menggagalkan request
        }

        return $response;
    }

    private function write(Request $request, Response $response, int $ms): void
    {
        $route = $request->route();
        $action = $route ? ltrim(str_replace('App\\Http\\Controllers\\', '', (string) $route->getActionName()), '\\') : '-';
        $status = $response->getStatusCode();
        $failure = $this->failure($response);

        $level = $status >= 500 ? 'error' : (($status >= 400 || $failure !== null) ? 'warning' : 'info');

        $context = [
            'user' => $this->user(),
            'ip' => $request->ip(),
            'ms' => $ms,
        ];
        $input = $this->clean($request->except(['_token', '_method']) + $request->allFiles());
        if ($input) {
            $context['input'] = $input;
        }
        if ($failure !== null) {
            $context['failure'] = $failure;
        }

        Log::log($level, sprintf('%s %s -> %s %d', $request->method(), $request->path(), $action, $status), $context);
    }

    /** Email user yang login (admin / tenant), kosong untuk API. */
    private function user(): string
    {
        if (!Session::isStarted()) {
            return '-';
        }

        return (string) (Session::get('login_email') ?: Session::get('Tsemail') ?: Session::get('Tenemail') ?: '-');
    }

    private function clean(array $data, int $depth = 0): array
    {
        $out = [];
        foreach ($data as $key => $value) {
            if (preg_match(self::SECRET, (string) $key)) {
                $out[$key] = '***';
            } elseif ($value instanceof UploadedFile) {
                $out[$key] = 'file:' . $value->getClientOriginalName() . ' (' . $value->getSize() . ' B)';
            } elseif (is_array($value)) {
                $out[$key] = $depth < 3 ? $this->clean($value, $depth + 1) : '[...]';
            } elseif (is_string($value) && strlen($value) > 500) {
                $out[$key] = substr($value, 0, 500) . '...(' . strlen($value) . ' B)';
            } else {
                $out[$key] = $value;
            }
        }

        return $out;
    }

    /** Pesan gagal dari isi respons (JSON / teks pendek), atau null kalau tidak gagal. */
    private function failure(Response $response): ?string
    {
        $body = $response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse
            || $response instanceof \Symfony\Component\HttpFoundation\StreamedResponse
            ? '' : (string) $response->getContent();
        if ($body === '' || strlen($body) > 200000) {
            return null;
        }

        $json = json_decode($body, true);
        if (is_array($json)) {
            $status = strtolower((string) ($json['status'] ?? ''));
            $failed = in_array($status, ['fail', 'failed', 'error'], true)
                || (array_key_exists('success', $json) && $json['success'] === false)
                || (!empty($json['Error']) && $json['Error'] === true)
                || (!empty($json['error']) && is_string($json['error']));
            if (!$failed) {
                return null;
            }
            $msg = $json['pesan'] ?? $json['message'] ?? $json['Pesan'] ?? $json['error'] ?? $status;

            return mb_substr(is_string($msg) ? $msg : json_encode($msg), 0, 1000);
        }

        // respons teks pendek (mis. WsbangunController: "Bad request", "Insert failed: ...")
        if (strlen($body) <= 1000 && !str_contains($body, '<') && preg_match('/bad request|failed|gagal/i', $body)) {
            return trim($body);
        }

        return null;
    }
}
