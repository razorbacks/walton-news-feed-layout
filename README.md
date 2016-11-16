# Walton College News Publication Scheduler

Schedule news publications to be generated periodically for static inclusion
on a website.

## Getting Started

Requires at least PHP 5.3.3 and [cron][2].

Install dependencies with [composer][1].

    composer install --no-dev

A `publications` folder must be created with accessible permissions for the
web server user account.

    mkdir publications
    chmod 777 publications

The application interface is `index.php` and is straight-forward.

[1]:https://getcomposer.org/
[2]:https://en.wikipedia.org/wiki/Cron
