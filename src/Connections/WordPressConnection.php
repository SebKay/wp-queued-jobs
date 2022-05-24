<?php

namespace WpQueuedJobs\Connections;

use WpQueuedJobs\Jobs\Job;

class WordPressConnection extends Connection
{
    public function getJobs(): array
    {
        global $wpdb;

        return \array_map(function ($result) {
            return \unserialize($result->option_value);
        }, $wpdb->get_results("SELECT * FROM wp_options WHERE option_name LIKE 'wpj_job_%' ORDER BY option_id") ?: []);
    }

    public function saveJob(Job $job): bool
    {
        return \update_option("wpj_job_{$job->getUuid()}", $job);
    }

    public function clearJob(string $uuid): bool
    {
        return \delete_option("wpj_job_{$uuid}");
    }

    public function lock(): bool
    {
        return \set_transient($this->lockName, \time(), 0);
    }

    public function unlock(): bool
    {
        return \delete_transient($this->lockName);
    }

    public function isLocked(): bool
    {
        return \get_transient($this->lockName) !== false;
    }
}
