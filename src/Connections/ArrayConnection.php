<?php

namespace WpQueuedJobs\Connections;

use WpQueuedJobs\Interfaces\Connection;
use WpQueuedJobs\Jobs\Job;

class ArrayConnection implements Connection
{
    protected array $jobs = [];

    public function saveJob(Job $jobs)
    {
        $this->jobs[] = $jobs;
    }

    public function getJobs()
    {
        return $this->jobs;
    }

    public function hasJobs()
    {
        return !empty($this->jobs);
    }
}
