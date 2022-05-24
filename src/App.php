<?php

namespace WpQueuedJobs;

use WpQueuedJobs\Connections\WordPressConnection;
use WpQueuedJobs\Cron\Cronable;
use WpQueuedJobs\Interfaces\Connection;
use WpQueuedJobs\Queues\Queue;

class App extends Cronable
{
    protected Logger $logger;

    /**
     * @var Queue[]
     */
    protected array $queues = [];

    protected string $defaultQueue = 'default';

    protected bool $cronEnabled      = true;
    protected string $cronActionName = 'run_queues';

    public function __construct()
    {
        parent::__construct();

        $this->logger = new Logger();

        $this->queues[] = new Queue($this->defaultQueue, new WordPressConnection(), $this->logger);
    }

    protected function setupWpCron()
    {
        if ($this->cronEnabled) {
            $default_cron_in_minutes = Utilities::defaultCronInMinutes();

            \add_action($this->cronName(), function () {
                $this->run();
            });

            if (!\wp_next_scheduled($this->cronName())) {
                \wp_schedule_event(
                    \strtotime("+ {$default_cron_in_minutes} minutes"),
                    'lowest_cron_possible',
                    $this->cronName()
                );
            }
        } else {
            \wp_unschedule_event(\wp_next_scheduled($this->cronName()), $this->cronName());
        }
    }

    /**
     * @return Queue|null
     */
    public function addQueue(string $name, Connection $connection = null)
    {
        if ($name == $this->defaultQueue) {
            return $this->getDefaultQueue();
        }

        $connection = $connection ?? new WordPressConnection();

        $queue = new Queue($name, $connection, $this->logger);

        $this->queues[] = $queue;

        return $queue;
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

    public function addJob(string $class_name, $data = null)
    {
        $this
            ->getDefaultQueue()
            ->addJob($class_name, $data);

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
