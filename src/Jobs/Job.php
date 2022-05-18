<?php

namespace WpQueuedJobs\Jobs;

use Ramsey\Uuid\Uuid;
use WpQueuedJobs\Interfaces\Queueable;

abstract class Job implements Queueable
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
}
