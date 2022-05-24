<?php

namespace WpQueuedJobs\Queues;

use WpQueuedJobs\Interfaces\Connection;
use WpQueuedJobs\Jobs\Job;
use WpQueuedJobs\Logger;

class Queue
{
    protected Logger $logger;

    public string $name;
    protected array $jobs = [];

    protected Connection $connection;

    protected string $lockName = '';

    public function __construct(string $name, Connection $connection, Logger $logger)
    {
        $this->logger = $logger;

        $this->name       = $name;
        $this->connection = $connection;

        $this->lockName = "wpj_lock_queue_{$this->name}";
    }

    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function addJob(string $class_name, $data = null)
    {
        if (\class_exists($class_name) && \in_array(Job::class, \class_parents($class_name))) {
            $this->jobs[] = new $class_name($data);
        }

        return $this;
    }

    public function getJobs()
    {
        return $this->jobs;
    }

    public function hasJobs()
    {
        return !empty($this->getJobs());
    }

    public function dispatch()
    {
        $this->logger->general()->info("Dispatching jobs.", [
            'queue' => $this->name,
            'jobs'  => count($this->jobs),
        ]);

        foreach ($this->jobs as $job_key => $job) {
            $this->connection->saveJob($job);

            unset($this->jobs[$job_key]);
        }
    }

    protected function lock()
    {
        \set_transient($this->lockName, \time(), 0);
    }

    protected function unlock()
    {
        \delete_transient($this->lockName);
    }

    protected function isLocked()
    {
        return \get_transient($this->lockName) !== false;
    }

    public function run()
    {
        $jobs = $this->connection->getJobs();

        if (!$jobs || $this->isLocked()) {
            return;
        }

        $this->lock();

        $this->logger->general()->info("Started running jobs.", [
            'queue' => $this->name,
            'jobs'  => count($jobs),
        ]);

        foreach ($jobs as $job) {
            try {
                $job->handle();
            } catch (\Exception $e) {
                $this->logger->general()->error("Error running job.", [
                    'queue'     => $this->name,
                    'job'       => $job,
                    'exception' => $e,
                ]);
            } finally {
                $this->connection->clearJob($job->getUuid());
            }
        }

        $this->unlock();

        $this->logger->general()->info("Finished running jobs.", [
            'queue' => $this->name,
            'jobs'  => count($jobs),
        ]);
    }
}
