<?php

use WpQueuedJobs\App;
use WpQueuedJobs\Utilities;

const WPJ_ROOT_DIR = get_home_path();

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

if (function_exists('add_filter')) {
    \add_filter('cron_schedules', function ($schedules) {
        $schedules['lowest_cron_possible'] = [
            'interval' => Utilities::defaultCronTimeout(),
            'display'  => Utilities::defaultCronInMinutes() . " minute(s)",
        ];

        return $schedules;
    }, 10, 1);
}
