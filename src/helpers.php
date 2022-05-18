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

if (function_exists('add_action')) {
    add_action('init', function () {
        wpj();
    }, 10, 0);
}
