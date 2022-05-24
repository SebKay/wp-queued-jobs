<?php

namespace WpQueuedJobs\Connections;

use Ramsey\Uuid\Uuid;
use WpQueuedJobs\Interfaces\Connection as ConnectionInterface;

abstract class Connection implements ConnectionInterface
{
    protected string $lockName;

    public function __construct()
    {
        $this->lockName = 'wpj_lock_' . Uuid::uuid4();
    }
}
