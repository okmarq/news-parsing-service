FROM php:7.4-apache

WORKDIR /var/www/html

RUN a2enmod rewrite

RUN apt-get update \
  && apt-get install -y \
  librabbitmq-dev libssh-dev libzip-dev git wget unzip zip --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
  && docker-php-ext-install sockets \
  &&  pecl install amqp \
  && docker-php-ext-enable amqp \
  && pecl install apcu \
  && docker-php-ext-enable apcu

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd mysqli pdo_mysql bcmath zip intl opcache

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=composer:2.0 /usr/bin/composer /usr/local/bin/composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash

RUN apt-get install symfony-cli

COPY ./docker/server/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

COPY ./docker/server/php/php_amqp.dll /etc/bin/php/php7.4.26/ext

COPY ./app /var/www/html

CMD ["apache2-foreground"]

ENTRYPOINT ["/entrypoint.sh"]
