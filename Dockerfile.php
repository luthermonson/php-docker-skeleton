FROM php:7-fpm

ENV REDIS_VERSION 5.2.1

RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN apt-get update && apt-get install -y curl libcurl4-gnutls-dev libxml2-dev libxrender-dev libfontconfig libxext-dev
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install gettext
RUN docker-php-ext-install curl
RUN docker-php-ext-install simplexml
RUN docker-php-ext-install intl
RUN docker-php-ext-install bcmath
RUN pecl install mongodb

RUN apt-get update -y && apt-get install -y libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev \
libfreetype6-dev
RUN apt-get update && \
apt-get install -y \
zlib1g-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

RUN curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/$REDIS_VERSION.tar.gz \
&& tar xfz /tmp/redis.tar.gz \
&& rm -r /tmp/redis.tar.gz \
&& mkdir -p /usr/src/php/ext \
&& mv phpredis-* /usr/src/php/ext/redis

RUN docker-php-ext-install redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && apt-get install -y libzip-dev git zip
RUN docker-php-ext-install zip
RUN echo "extension=mongodb.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
RUN curl -sSL https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.buster_amd64.deb \
-o wkhtmltopdf.deb && \
apt install -y ./wkhtmltopdf.deb
