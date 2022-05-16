<?php

namespace WpQueuedJobs;

use WpQueuedJobs\Connections\WordPressConnection;
use WpQueuedJobs\Cron\Cronable as CronCronable;
use WpQueuedJobs\Interfaces\Connection;
use WpQueuedJobs\Queues\Queue;

class App extends CronCronable
{
    /**
     * @var Queue[]
     */
    protected array $queues = [];

    protected string $defaultQueue = 'default';

    protected bool $cron_enabled       = false;
    protected string $cron_action_name = 'run_queues';

    public function __construct()
    {
        $this->queues[] = new Queue($this->defaultQueue, new WordPressConnection());

        $this->setupWpCron();
    }

    protected function setupWpCron()
    {
        $default_cron_in_minutes = \intval(\gmdate('i', Utilities::defaultCronTimeout()));

        \add_filter('cron_schedules', function ($schedules) use ($default_cron_in_minutes) {
            $schedules['one_minute'] = [
                'interval' => 60,
                'display'  => 'Every Minute',
            ];

            $schedules['lowest_cron_possible'] = [
                'interval' => Utilities::defaultCronTimeout(),
                'display'  => "{$default_cron_in_minutes} minute(s)",
            ];

            return $schedules;
        });

        if ($this->cronEnabled()) {
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
