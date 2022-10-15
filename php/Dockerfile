FROM php:7.4-fpm

WORKDIR /var/www/symfony_docker

RUN apt update \
  && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
  && docker-php-ext-install intl opcache pdo pdo_mysql \
  && pecl install apcu \
  && docker-php-ext-enable apcu \
  && docker-php-ext-configure zip \
  && docker-php-ext-install zip

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash

RUN apt install symfony-cli

COPY entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

COPY ./app /var/www/symfony_docker/app

RUN chmod -R ugo+rw /var/www/symfony_docker/app/var/log

RUN chmod -R ugo+rw /var/www/symfony_docker/app/var/cache

RUN chown -R www-data /var/www/symfony_docker/appvar/log/

RUN chown -R www-data /var/www/symfony_docker/appvar/cache/

ENTRYPOINT ["/entrypoint.sh"]
