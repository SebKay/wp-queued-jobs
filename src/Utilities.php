<?php

namespace WpQueuedJobs;

class Utilities
{
    public static function defaultCronTimeout()
    {
        if (\defined('WP_CRON_LOCK_TIMEOUT')) {
            return \WP_CRON_LOCK_TIMEOUT;
        }

        return 60;
    }
}
