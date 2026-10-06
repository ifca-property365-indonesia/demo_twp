<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\LogRecord;

/**
 * Menulis setiap record ke file milik controller yang sedang menangani request
 * (lihat ControllerLog::path). Satu StreamHandler per file, dibuat saat pertama dipakai;
 * folder dibuat otomatis.
 */
class ControllerFileHandler extends AbstractProcessingHandler
{
    /** @var array<string, StreamHandler> */
    private array $streams = [];

    public function __construct(int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        $path = ControllerLog::path();

        if (!isset($this->streams[$path])) {
            // Folder tanggal belum ada = log pertama hari ini -> sekalian hapus log lama
            // (tanpa perlu cron; lihat ControllerLog::prune).
            if (!is_dir(storage_path('logs/' . date('Y-m-d')))) {
                try {
                    ControllerLog::prune();
                } catch (\Throwable $e) {
                    // gagal hapus log lama tidak boleh menggagalkan penulisan log
                }
            }

            $stream = new StreamHandler($path, $this->level, true, null, true);
            $stream->setFormatter($this->getFormatter());
            $this->streams[$path] = $stream;
        }

        $this->streams[$path]->handle($record);
    }

    protected function getDefaultFormatter(): LineFormatter
    {
        $formatter = new LineFormatter(null, 'Y-m-d H:i:s', true, true);
        $formatter->includeStacktraces();

        return $formatter;
    }

    public function close(): void
    {
        foreach ($this->streams as $stream) {
            $stream->close();
        }
        $this->streams = [];
        parent::close();
    }
}
