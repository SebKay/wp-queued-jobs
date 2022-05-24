<?php

namespace WpQueuedJobs\Jobs;

use Ramsey\Uuid\Uuid;
use WpQueuedJobs\Interfaces\Queueable;

abstract class Job implements Queueable
{
    protected string $uuid;

    protected $data = null;

    public function __construct($data = null)
    {
        $this->uuid = Uuid::uuid4();

        $this->data = $data;
    }

    public function getUuid()
    {
        return $this->uuid;
    }
}
