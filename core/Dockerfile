FROM php:7.3-apache

# install php extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# enable mod_rewrite
RUN a2enmod rewrite

# install and enable xdebug
RUN pecl install -f xdebug \
&& echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini;