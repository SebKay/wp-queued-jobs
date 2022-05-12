<?php

namespace WpQueuedJobs\Interfaces;

interface Queueable
{
    public function handle();
}
