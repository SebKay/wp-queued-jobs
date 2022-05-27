<?php

namespace WPTS\Tests\Integration;

use WPTS\Tests\Jobs\ExampleJob;

class AppTest extends IntegrationTest
{
    public function setUp(): void
    {
        parent::setUp();

        wpj();
    }

    protected function get_jobs_from_database()
    {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM wptests_options WHERE option_name LIKE 'wpj_job_%' ORDER BY option_id");
    }

    public function test_job_gets_pushed_to_database()
    {
        wpj()
            ->addJob(ExampleJob::class)
            ->addJob(ExampleJob::class)
            ->addJob(ExampleJob::class)
            ->dispatch();

        $this->assertNotEmpty($this->get_jobs_from_database());
        $this->assertCount(3, \count($this->get_jobs_from_database()));
    }

    public function test_job_gets_pushed_to_database_using_custom_queue()
    {
        wpj()
            ->addQueue('custom')
            ->addJob(ExampleJob::class)
            ->addJob(ExampleJob::class)
            ->addJob(ExampleJob::class)
            ->dispatch();

        $this->assertNotEmpty($this->get_jobs_from_database());
        $this->assertCount(3, \count($this->get_jobs_from_database()));
    }
}
