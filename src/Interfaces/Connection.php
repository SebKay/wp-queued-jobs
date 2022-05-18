<?php

namespace WpQueuedJobs\Interfaces;

use WpQueuedJobs\Jobs\Job;

interface Connection
{
    public function saveJob(Job $jobs);

    public function getJobs(): array;

    public function clearJob(string $uuid): bool;
}
