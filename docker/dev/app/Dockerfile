FROM php:7.3.3-fpm-alpine3.9

ARG UID

RUN if [ -z "$UID" ]; then echo "Build argument 'UID' was not set" 1>&2 && exit 1; fi

# Create a user account manually rather than using:
# adduser -D -u $UID -h /home/dev dev
# This is necessary to support $UIDs > 256000 on alpine/busybox.
RUN echo "dev:x:$UID:$UID::/home/dev:" >> /etc/passwd \
    && echo "dev:!:$(($(date +%s) / 60 / 60 / 24)):0:99999:7:::" >> /etc/shadow \
    && echo "dev:x:$UID:" >> /etc/group \
    && mkdir /home/dev && chown dev: /home/dev

# Set timezone
RUN echo "UTC" > /etc/timezone

RUN apk add --no-cache git rabbitmq-c-dev nodejs nodejs-npm g++ make autoconf icu-dev postgresql-dev

# Install php extensions.
RUN pecl install xdebug-beta \
    && pecl install amqp \
    && docker-php-ext-install intl \
    && docker-php-ext-enable amqp \
    && { \
        echo 'xdebug.remote_enable=1'; \
        echo 'xdebug.remote_autostart=1'; \
        echo 'xdebug.max_nesting_level=1200'; \
        echo 'xdebug.idekey = PHPSTORM'; \
    } > /usr/local/etc/php/conf.d/99-docker-php-ext-xdebug.ini \
    && docker-php-ext-enable xdebug --ini-name 99-docker-php-ext-xdebug.ini \
    && docker-php-ext-install pdo_pgsql

# Remove packages only needed for building PHP exts.
RUN apk del --rdepends g++ make autoconf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

WORKDIR /var/www

# Clean up /tmp.
RUN rm -rf /tmp/* /var/tmp/*

USER dev

# Install yarn.
# The version in the alpine package manager is ancient.
# Note that this has to happen after switching to the dev user as yarn installs in $HOME and there is no way to
# configure this like there is with composer.
RUN curl --compressed -o- -L https://yarnpkg.com/install.sh | sh

ENV PATH="/var/www/vendor/bin:/var/www/bin:/home/dev/.yarn/bin:/home/dev/.config/yarn/global/node_modules/.bin:${PATH}"

CMD ["php-fpm"]
