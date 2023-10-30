FROM php:8.2-fpm-alpine

RUN apk update && apk add git curl zip unzip autoconf g++ make linux-headers

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN echo "xdebug.coverage_enable" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

