<?php

namespace WPTS\Tests\Integration;

use WPTS\Tests\Jobs\ExampleJob;

class ExampleTest extends IntegrationTest
{
    public function setUp(): void
    {
        parent::setUp();

        wpj();
    }

    public function test_job_gets_pushed_to_database()
    {
        wpj()
            ->addJob(ExampleJob::class)
            ->dispatch();

        global $wpdb;

        $this->assertNotEmpty($wpdb->get_results("SELECT * FROM wptests_options WHERE option_name LIKE 'wpj_job_%' ORDER BY option_id"));
    }
}
