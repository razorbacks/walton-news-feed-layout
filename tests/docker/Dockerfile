FROM ubuntu:16.04

RUN apt-get update && apt-get install -y                                       \
        cron php-cli php-xml php-xdebug                                     && \
    apt-get autoremove -y && apt-get clean                                  && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /code

CMD ["./vendor/bin/phpunit"]
