FROM php:fpm-alpine

COPY wait-for-it.sh /usr/bin/wait-for-it

RUN chmod +x /usr/bin/wait-for-it

RUN apk --update --no-cache add git

RUN docker-php-ext-install pdo_mysql \
    && apk --no-cache add --update icu-dev \
    && docker-php-ext-install intl

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY ./cronjobs /etc/cron.d/scheduler

COPY ./entrypoint.sh /

ENTRYPOINT /entrypoint.sh

EXPOSE 9000
