<?php

namespace WpQueuedJobs\Cron;

abstract class Cronable
{
    protected bool $cronEnabled = true;

    protected string $cronActionPrefix = 'wpj_';
    protected string $cronActionName   = '';

    public function __construct()
    {
        $this->setupWpCron();
    }

    public function cronName()
    {
        return $this->cronActionPrefix . $this->cronActionName;
    }

    protected function setupWpCron()
    {
    }
}
