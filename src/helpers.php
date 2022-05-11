<?php

use WpQueuedJobs\App;

if (!function_exists('wpj')) {
    function wpj(): App
    {
        global $app;

        if (!isset($app)) {
            $app = new App();
        }

        return $app;
    }
}
