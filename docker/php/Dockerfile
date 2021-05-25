### PHP
ARG PHP_VERSION=7.4

FROM php:${PHP_VERSION}-alpine AS docker_php

ARG GID
ARG UID

RUN set -eux \
  && apk --no-cache add \
    libzip-dev \
    libpng-dev \
    git \
	icu-libs \
	bash \
	autoconf \
	g++ \
	make \
	icu-dev \
	gcc \
	libtool

RUN docker-php-ext-configure zip \
	&& docker-php-ext-install \
		intl \
		zip \
		bcmath \
		sockets \
		gd

RUN apk del --purge autoconf g++ make gcc

# Init user
RUN addgroup -g 2000 -S app && adduser -G app -u ${UID} -s /bin/bash -S app && sed -i "s/2000/${GID}/" /etc/group

# Configure Timezone
RUN ln -fs /usr/share/zoneinfo/Europe/Paris /etc/localtime && \
    echo "Europe/Paris" > /etc/timezone

# Install composer
RUN curl https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

# Link php-fpm version to php-fpm to be independant
RUN ln -s /usr/sbin/php-fpm${PHP_VERSION} /usr/sbin/php-fpm && \
    mkdir /run/php

USER app

WORKDIR /var/www

CMD ["php-fpm", "-F"]

EXPOSE 9000
