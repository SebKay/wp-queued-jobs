<?php

namespace WpQueuedJobs\Cron;

abstract class Cronable
{
    protected bool $cron_enabled = true;

    protected string $cron_action_prefix = 'wpj_';
    protected string $cron_action_name = '';

    public function cronName()
    {
        return $this->cron_action_prefix . $this->cron_action_name;
    }

    public function cronEnabled()
    {
        return $this->cron_enabled;
    }
}
