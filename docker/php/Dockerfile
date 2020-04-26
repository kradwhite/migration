FROM php:7.4-fpm
RUN apt-get update && apt-get upgrade \
    && docker-php-ext-install -j$(nproc) mysqli pdo_mysql pdo_pgsql
ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini
WORKDIR /var/www
CMD ["php-fpm"]