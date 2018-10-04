FROM php:7.2-apache

COPY ./app /var/www/app
COPY ./bin /var/www/bin
COPY ./config /var/www/config
COPY ./src /var/www/src

COPY ./web/app.php /var/www/web/app.php
COPY ./web/app_dev.php /var/www/web/app_dev.php
COPY ./web/favicon.ico /var/www/web/favicon.ico
COPY ./web/robots.txt /var/www/web/robots.txt

COPY ./docker/composer.json /var/www/composer.json
COPY ./docker/conf/ /etc/apache2

EXPOSE 80

WORKDIR /var/www
RUN ls -la
RUN ls -la app
RUN ls -la app/config
RUN ls -la ../


RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd

RUN apt-get update && apt-get install -y zlib1g-dev \
    && docker-php-ext-install zip

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update -y && apt-get install -y wget git zip unzip nano sudo

RUN wget https://getcomposer.org/composer.phar
RUN php composer.phar install

RUN php bin/console cache:clear -vvv
RUN php bin/console assets:install -vvv

RUN a2ensite 000-* 001-*
RUN service apache2 start

RUN HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
RUN chmod -R 777 var
