<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

/** Channel log 'controller' (config/logging.php): satu file per controller per hari. */
class CreateControllerLogger
{
    public function __invoke(array $config): Logger
    {
        return new Logger('twp', [new ControllerFileHandler($config['level'] ?? 'debug')], [new PsrLogMessageProcessor()]);
    }
}
