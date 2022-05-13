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

    public function getJobs()
    {
        return $this->jobs;
    }

    public function clearJob(string $uuid)
    {
        $jobs = parent::clearJob($uuid);

        $this->jobs = $jobs;

        return $jobs;
    }

    public function clearJobs()
    {
        $this->jobs = [];
    }
}
