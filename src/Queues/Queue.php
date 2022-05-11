<?php

namespace WpQueuedJobs\Queues;

use WpQueuedJobs\Jobs\Job;

class Queue
{
    public string $name;
    protected array $jobs = [];

    public function __construct(string $name)
    {
        $this->name = $name;
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

    public function run()
    {
        \ray('Running jobs in queue: ' . $this->name);

        foreach ($this->jobs as $job) {
            $job->handle();
        }

        \ray('Finished running jobs in queue: ' . $this->name);
    }
}
