<?php

namespace WpQueuedJobs\Queues;

use WpQueuedJobs\Jobs\Job;

class Queue
{
    public string $name;
    protected array $jobs           = [];
    protected array $dispatchedJobs = [];

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

    public function hasDispatchedJobs()
    {
        return !empty($this->dispatchedJobs);
    }

    public function dispatch()
    {
        foreach ($this->jobs as $job_key => $job) {
            $this->dispatchedJobs[] = $job;

            unset($this->jobs[$job_key]);
        }
    }

    public function run()
    {
        if (!$this->hasDispatchedJobs()) {
            return;
        }

        \ray('Running jobs in queue: ' . $this->name);

        foreach ($this->dispatchedJobs as $job) {
            $job->handle();
        }

        \ray('Finished running jobs in queue: ' . $this->name);
    }
}
