# WP Queued Jobs

[![Validate PHP](https://github.com/SebKay/wp-queued-jobs/actions/workflows/validate-php.yml/badge.svg)](https://github.com/SebKay/wp-queued-jobs/actions/workflows/validate-php.yml)

A [Laravel](https://laravel.com/)-like queue system for [WordPress](https://wordpress.org/).

Easily create background jobs for things like sending emails or importing large amounts of data. All with a fluent API.

I highly recommend using a plugin like [WP Crontrol](https://wordpress.org/plugins/wp-crontrol/) so you can easily see, and manually run, cron jobs from the WordPress dashboard.

**Requires PHP 7.4+**

***

## Install

The recommended way to install this package is via [Composer](https://getcomposer.org/).

```shell
composer require sebkay/wp-queued-jobs
```

## Usage

### 1. Create job

To create a job, you need to extend the `WpQueuedJobs\Jobs\Job` class:

```php
use WpQueuedJobs\Jobs\Job;

class BackgroundJob extends Job
{
    public function handle()
    {
        // Handle the job
        // Use $this->data to access what was passed with the job when it was added to the queue
    }
}
```

### 2. Add job to queue and dispatch

```php
wpj()
    ->addJob(BackgroundJob::class, 'Data for the background job.')
    ->dispatch();
```

Anything passed as the second parameter to `addJob()` will be available in the `handle()` method of the job (and the rest of the class) as `$this->data`.

The data can be anything you want. A `string`, `array`, `integer`, `class` etc...

The "queue worker" will look for new jobs every 1 minute and run them (if there are any).

The system runs on a "first-in first-out" basis. So whatever gets dispatched first will be run first.

#### Important

As the lowest cron time in WordPress defaults to 1 minute, jobs scheduled to run at 6am might not actually run until 6:01am. This isn't an issue in the majority of cases, but it's worth knowing.

The point of background jobs is that they run in the background, not immediately, so they should never be used for time-sensitive tasks.

## Example #1 (Send Email)

1. Load the "successful registration" page template.
2. Add the "send welcome email" job and dispatch it to the queue.

```php
add_action('template_redirect', function () {
    if (!is_page_template('register-success.php')) {
        return;
    }

    wpj()
        ->addJob(SendWelcomeEmailJob::class, wp_get_current_user())
        ->dispatch();
}, 10, 0);
```

## Example #2 (Import Posts from API)

1. Create a custom cron event.
2. Add two jobs and dispatch them to the queue.
3. Each job is given an `offset` and a `max` so the same posts aren't imported twice.
4. Schedule the cron even to run once per day at 6am, starting tomorrow.

```php
add_action('import_api_posts', function () {
    wpj()
        ->addJob(ImportApiPostsJob::class, ['offset' => 0, 'max' => 100])
        ->addJob(ImportApiPostsJob::class, ['offset' => 100, 'max' => 100])
        ->dispatch();
}, 10, 0);

if (!\wp_next_scheduled('import_api_posts')) {
    \wp_schedule_event(
        \strtotime("+ 1 days 6am"),
        'daily',
        'import_api_posts'
    );
}
```

## Why

Unfortunatetly there's no concept of a "queue worker" in WordPress. At it's core WordPress is just a bunch of PHP files. There's no command line runner, such as [Artisan](https://laravel.com/docs/9.x/artisan) in Laravel.

Although the [WordPress CLI](https://wp-cli.org/) exists, a lot of people host their sites on shared hosting, so that isn't an option.

This package tries to circumvent that by utilising the WordPress Cron system. It runs a WP Cron task every minute to see if there are new jobs to run. If there are then it locks the connection to the database, runs the jobs, then unlocks the connection.

It's important to lock the queue to prevent the same jobs from running multiple times. The "queue worker" checks if there are new jobs every minute, so if any queue takes longer than a minute to finish jobs won't overlap.
