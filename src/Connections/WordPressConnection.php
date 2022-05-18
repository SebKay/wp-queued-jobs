<?php

namespace WpQueuedJobs\Connections;

use Ramsey\Uuid\Uuid;
use WpQueuedJobs\Jobs\Job;

class WordPressConnection extends Connection
{
    public function saveJob(Job $job)
    {
        \update_option("wpj_job_{$job->getUuid()}", $job);
    }

    public function getJobs(): array
    {
        global $wpdb;

        return \array_map(function ($result) {
            return \unserialize($result->option_value);
        }, $wpdb->get_results("SELECT * FROM wp_options WHERE option_name LIKE 'wpj_job_%' ORDER BY option_id") ?: []);
    }

    public function clearJob(string $uuid): bool
    {
        return \delete_option("wpj_job_{$uuid}");
    }
}
