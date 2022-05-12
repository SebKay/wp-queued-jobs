<?php

namespace WpQueuedJobs\Interfaces;

use WpQueuedJobs\Jobs\Job;

interface Connection
{
    public function saveJob(Job $jobs);

    public function getJobs();

    public function hasJobs();
}
