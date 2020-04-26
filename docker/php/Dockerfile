FROM php:7.4-fpm
ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini
WORKDIR /root/app
RUN apt-get update && apt-get upgrade && apt-get install -y libpq-dev \
&& docker-php-ext-install -j$(nproc) mysqli pdo_mysql pdo_pgsql
CMD ["php-fpm"]