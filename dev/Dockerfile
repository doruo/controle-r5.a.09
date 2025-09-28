FROM php:8.4-apache

RUN docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable pdo_mysql

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug


RUN mkdir -p /tmp/xdebug \
    && chown www-data:www-data /tmp/xdebug

RUN tail -f /tmp/xdebug/



COPY src/php/xdebug.ini /usr/local/etc/php/conf.d/

