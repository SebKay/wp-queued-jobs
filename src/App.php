<?php

namespace WpQueuedJobs;

use WpQueuedJobs\Connections\ArrayConnection;
use WpQueuedJobs\Interfaces\Connection;
use WpQueuedJobs\Queues\Queue;

class App
{
    /**
     * @var Queue[]
     */
    protected array $queues = [];

    protected string $defaultQueue = 'default';

    public function __construct()
    {
        $this->queues[] = new Queue($this->defaultQueue, new ArrayConnection());
    }

    public function addQueue(string $name, Connection $connection)
    {
        if ($name !== $this->defaultQueue) {
            $this->queues[] = new Queue($name, $connection);
        }

        return $this;
    }

    /**
     * @return Queue|null
     */
    public function getQueue(string $name)
    {
        foreach ($this->queues as $queue) {
            if ($queue->name === $name) {
                return $queue;
            }
        }
    }

    protected function getDefaultQueue()
    {
        return $this->getQueue($this->defaultQueue);
    }

    public function addJob(string $class_name)
    {
        $this
            ->getDefaultQueue()
            ->addJob($class_name);

        return $this;
    }

    public function dispatch()
    {
        $this
            ->getDefaultQueue()
            ->dispatch();

        return $this;
    }

    public function run()
    {
        foreach ($this->queues as $queue) {
            $queue->run();
        }
    }
}
