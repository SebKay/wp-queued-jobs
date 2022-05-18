<?php

namespace WpQueuedJobs\Connections;

use WpQueuedJobs\Jobs\Job;

class ArrayConnection extends Connection
{
    protected array $jobs = [];

    public function saveJob(Job $job)
    {
        $this->jobs[] = $job;
    }

    public function getJobs(): array
    {
        return $this->jobs;
    }

    public function clearJob(string $uuid): bool
    {
        $jobs = $this->getJobs();

        foreach ($jobs as $job_key => $job) {
            if ($job->getUuid() === $uuid) {
                unset($jobs[$job_key]);
            }
        }

        $this->jobs = $jobs;

        return true;
    }
}
