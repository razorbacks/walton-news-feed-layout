# Walton College News Publication Scheduler

Schedule news publications to be generated periodically for static inclusion
on a website.

[![Build Status][4]][3]

## Getting Started

Requires at least PHP 5.3.3 and [cron][2].

### Composer Install

Install dependencies with [composer][1].

    composer install --no-dev

### Create Publications Folder

A `publications` folder must be created with write permissions for the
web server user account.

Permissive example granting everyone full access:

    mkdir publications
    chmod 777 publications

More secure example for web server running as `www-data`

    mkdir publications
    chown www-data publications

### Environment Variables

You need to create an `.env` file setting the values in [`example.env`][6]

    cp example.env .env

This file may be left blank if setting the environment variables another way.

## Start

The application interface is `index.php` and is straight-forward.

It is recommended to secure the application with authentication.
An example `.htaccess` file is included for [Shibboleth][5].

[1]:https://getcomposer.org/
[2]:https://en.wikipedia.org/wiki/Cron
[3]:https://travis-ci.org/razorbacks/walton-news-publication-scheduler
[4]:https://travis-ci.org/razorbacks/walton-news-publication-scheduler.svg?branch=master
[5]:https://shibboleth.net/
[6]:./example.env
