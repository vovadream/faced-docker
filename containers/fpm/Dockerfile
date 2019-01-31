FROM php:fpm
MAINTAINER nickname <my-email@domain>

RUN apt-get update && apt-get install -y \
        libmcrypt-dev \
        && apt-get install -y libpq-dev \
        && docker-php-ext-install -j$(nproc) mcrypt \
        && pecl install mongodb
        && docker-php-ext-enable mongodb
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install exif
RUN docker-php-ext-install opcache

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql pdo_pgsql
COPY conf/ /usr/local/etc/php-fpm.d/
CMD ["php-fpm"]
