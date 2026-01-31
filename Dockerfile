FROM mparvin/codeigniter-base:7.3-fpm

RUN apt-get update && \
    apt-get install -y libxml2-dev

RUN docker-php-ext-install soap

WORKDIR /var/www

COPY .configs/php.ini /usr/local/etc/php/php.ini

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

#RUN composer require aws/aws-sdk-php

