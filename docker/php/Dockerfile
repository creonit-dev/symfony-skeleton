FROM php:7.4-fpm

RUN apt-get update; \
    apt-get install -y --no-install-recommends \
        openssl \
        git \
        zlibc \
        zlib1g \
        zlib1g-dev \
        libfreetype6-dev \
        libssl-dev \
        libjpeg62-turbo-dev \
        libmagickwand-dev \
        libmcrypt-dev \
        libpng-dev \
        libicu-dev \
        libzip-dev \
        zip \
        unzip \
    ; \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/; \
    docker-php-ext-install pdo_mysql gd intl opcache soap zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && mkdir /.composer/ \
    && chmod 777 /.composer/

# Install Symfony
RUN  curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony/bin/symfony /usr/local/bin/symfony \
    && mkdir /.symfony/ \
    && chmod 777 /.symfony/