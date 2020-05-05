FROM php:7.4-fpm
ADD php.ini /usr/local/etc/php/php.ini
WORKDIR /root/app
RUN apt-get update && apt-get upgrade -y && apt-get install -y curl wget libpq-dev libyaml-dev \
&& pecl install xdebug yaml \
&& docker-php-ext-enable xdebug yaml \
&& docker-php-ext-configure pgsql \
&& docker-php-ext-install pdo mysqli pdo_mysql pdo_pgsql
CMD ["php-fpm"]