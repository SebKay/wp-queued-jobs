<?php

namespace WpQueuedJobs\Interfaces;

use WpQueuedJobs\Jobs\Job;

interface Connection
{
    public function getJobs(): array;

    public function saveJob(Job $jobs): bool;

    public function clearJob(string $uuid): bool;

    public function lock(): bool;

    public function unlock(): bool;

    public function isLocked(): bool;
}
