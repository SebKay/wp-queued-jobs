<?php

namespace WpQueuedJobs;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger as MonologLogger;

class Logger
{
    protected MonologLogger $general;

    /**
     * Set up
     */
    public function __construct()
    {
        $formatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s'
        );

        $handler = (new RotatingFileHandler(
            \WPJ_ROOT_DIR . '/atfss-logs/general/general.log',
            MonologLogger::DEBUG
        ))->setFormatter($formatter);

        $this->general = (new MonologLogger('ATFSS General'))->pushHandler($handler);
    }

    public function general(): \Monolog\Logger
    {
        return $this->general;
    }
}
