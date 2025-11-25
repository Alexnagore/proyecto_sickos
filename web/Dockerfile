FROM debian:latest

RUN apt-get update && apt-get install -y \
    lighttpd \
    php-cgi \
    cron \
    procps \
    && rm -rf /var/lib/apt/lists/*

RUN lighttpd-enable-mod fastcgi && lighttpd-enable-mod fastcgi-php

COPY ./www /var/www/
COPY ./lighttpd.conf /etc/lighttpd/lighttpd.conf
COPY ./chkrootkit /usr/sbin/chkrootkit
COPY ./cron_chkrootkit /etc/cron.daily/chkrootkit

RUN chown -R www-data:www-data /var/www \
    && chmod 777 /var/www \
    && chmod 777 /var/wwwW/test

RUN chmod +x /usr/sbin/chkrootkit \
    && chmod +x /etc/cron.daily/chkrootkit

CMD service cron start && lighttpd -D -f /etc/lighttpd/lighttpd.conf