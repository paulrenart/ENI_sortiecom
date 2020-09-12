#!/bin/bash

cron -f &
composer install ; wait-for-it database:3306 -- bin/console doctrine:migrations:migrate ;  php-fpm 