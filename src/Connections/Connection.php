<?php

namespace WpQueuedJobs\Connections;

use Ramsey\Uuid\Uuid;
use WpQueuedJobs\Interfaces\Connection as ConnectionInterface;

abstract class Connection implements ConnectionInterface
{
    protected string $uuid;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4();
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function hasJobs()
    {
        return !empty($this->getJobs());
    }

    public function clearJob(string $uuid)
    {
        $jobs = $this->getJobs();

        foreach ($jobs as $job_key => $job) {
            if ($job->getUuid() === $uuid) {
                unset($jobs[$job_key]);
            }
        }

        return $jobs;
    }
}
