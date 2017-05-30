#!/bin/bash

composer config -g -- disable-tls true
composer install --no-dev
