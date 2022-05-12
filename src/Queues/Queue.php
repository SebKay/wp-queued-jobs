<?php

namespace WpQueuedJobs\Queues;

use WpQueuedJobs\Interfaces\Connection;
use WpQueuedJobs\Jobs\Job;

class Queue
{
    public string $name;
    protected array $jobs = [];

    protected Connection $connection;

    public function __construct(string $name, Connection $connection)
    {
        $this->name       = $name;
        $this->connection = $connection;
    }

    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function addJob(string $class_name)
    {
        if (\class_exists($class_name) && \in_array(Job::class, \class_parents($class_name))) {
            $this->jobs[] = new $class_name();
        }

        return $this;
    }

    public function hasJobs()
    {
        return !empty($this->jobs);
    }

    public function dispatch()
    {
        foreach ($this->jobs as $job_key => $job) {
            $this->connection->saveJob($job);

            unset($this->jobs[$job_key]);
        }
    }

    public function run()
    {
        if (!$this->connection->hasJobs()) {
            return;
        }

        \ray('Running jobs in queue: ' . $this->name);

        foreach ($this->connection->getJobs() as $job) {
            $job->handle();
        }

        \ray('Finished running jobs in queue: ' . $this->name);
    }
}
