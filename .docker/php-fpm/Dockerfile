FROM php:8.2-fpm-alpine

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions gd xdebug opcache pdo_mysql zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ARG CURRENT_UID=1000
ENV CURRENT_UID ${CURRENT_UID}
ARG CURRENT_GID=1000
ENV CURRENT_GID ${CURRENT_GID}

RUN apk --no-cache add shadow && \
    groupmod -o -g ${CURRENT_GID} www-data && \
    usermod -o -u ${CURRENT_UID} -g www-data www-data

WORKDIR /app
