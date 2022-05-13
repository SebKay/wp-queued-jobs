<?php

namespace WpQueuedJobs\Connections;

use Ramsey\Uuid\Uuid;
use WpQueuedJobs\Jobs\Job;

class WordPressConnection extends Connection
{
    public function saveJob(Job $job)
    {
        $jobs = $this->getJobs();

        $jobs[] = $job;

        \update_option("wpj_jobs_{$this->uuid}", \serialize($jobs));
    }

    public function getJobs()
    {
        return \unserialize(\get_option("wpj_jobs_{$this->uuid}", ''));
    }

    public function clearJob(string $uuid)
    {
        $jobs = parent::clearJob($uuid);

        \update_option("wpj_jobs_{$this->uuid}", \serialize($jobs));

        return $jobs;
    }

    public function clearJobs()
    {
        \delete_option("wpj_jobs_{$this->uuid}");
    }
}
