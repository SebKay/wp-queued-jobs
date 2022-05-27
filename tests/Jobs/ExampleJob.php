<?php

namespace WPTS\Tests\Jobs;

use WpQueuedJobs\Jobs\Job;

class ExampleJob extends Job
{
    public function handle()
    {
        \ray('Handle BackgroundJob 1', $this->data);

        \sleep(3);
    }
}
