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

### Start

The application interface is `index.php` and is straight-forward.

It is recommended to secure the application with authentication.
An example `.htaccess` file is included for [Shibboleth][5].

## Creating Layouts

The [Twig layouts][10] are in the [`views` folder][9].

### Images

When available, image URLs are automatically retrieved in `item.image`

* `item.image.thumbnail`
* `item.image.medium`
* `item.image.medium_large`
* `item.image.large`
* `item.image.full`

Defaults for these values can be set in the `.env` file.

### Unescaped Data

The Wordpress REST API returns rendered (HTML-encoded) responses for
title, content, and excerpt.
These values have been collapsed for convenience in view references, for example
`item.title` instead of `item.title.rendered`.

Everything else is passed through directly from the JSON feed.

Most other things don't need escaping.
The Twig template engine has been configured to not [autoescape][7] data,
so be mindful of this when extending views, especially with custom endpoints
to prevent broken HTML entities and XSS attacks.
You can easily enable escaping in the views like this:

```
{% autoescape %}
    Everything will be automatically escaped in this block
    using the HTML strategy
{% endautoescape %}
```

Or selectively apply it with the [escape][8] filter.

```
{{ user.username|escape }}
```

## Testing

The tests will drop and modify your crontab as part of the fixtures,
so you should run them inside a docker container.

    docker build -t razorbacks/test-cron ./tests/docker
    docker run --rm -it -v "$PWD":/code razorbacks/test-cron

[1]:https://getcomposer.org/
[2]:https://en.wikipedia.org/wiki/Cron
[3]:https://travis-ci.org/razorbacks/walton-news-publication-scheduler
[4]:https://travis-ci.org/razorbacks/walton-news-publication-scheduler.svg?branch=master
[5]:https://shibboleth.net/
[6]:./example.env
[7]:https://twig.sensiolabs.org/doc/2.x/tags/autoescape.html
[8]:https://twig.sensiolabs.org/doc/2.x/filters/escape.html
[9]:./views
[10]:https://twig.sensiolabs.org/doc/2.x/
