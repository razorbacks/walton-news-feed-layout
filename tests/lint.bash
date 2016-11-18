#!/bin/bash

shopt -s globstar

set -e

for x in **/*php; do
	# this file is actually ignored at runtime if incompatible
	# https://github.com/composer/composer/issues/5324
	if [ "$x" != "vendor/composer/autoload_static.php" ]; then
		php -l "$x";
	fi
done
