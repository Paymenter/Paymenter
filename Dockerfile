# Stage 1:
# Build the actual container with all of the needed PHP dependencies that will run the application.
FROM --platform=$TARGETOS/$TARGETARCH dunglas/frankenphp:php8.3-alpine AS final
WORKDIR /app

RUN apk add --no-cache --update ca-certificates dcron curl git supervisor tar unzip libpng-dev libxml2-dev libzip-dev icu-dev linux-headers gmp-dev nodejs npm \
    && install-php-extensions bcmath gd pdo_mysql zip intl sockets gmp redis opcache pcntl igbinary apcu

# Opcache configuration for high performance
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.enable_file_override=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.interned_strings_buffer=64" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=30000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.save_comments=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.jit_buffer_size=100M" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "realpath_cache_size=10M" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "realpath_cache_ttl=7200" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json ./
RUN composer install --no-dev --no-autoloader --no-scripts

COPY . ./
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/framework/testing bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

RUN composer install --no-dev --optimize-autoloader --apcu-autoloader --prefer-dist

RUN cp .env.example .env \
    && chmod 777 -R bootstrap storage/* \
    && rm -rf .env bootstrap/cache/*.php \
    && echo "* * * * * /usr/local/bin/php /app/artisan schedule:run >> /dev/null 2>&1" >> /var/spool/cron/crontabs/root \
    && mkdir -p /var/run/php

FROM --platform=$TARGETOS/$TARGETARCH node:22-alpine AS build
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

COPY . ./
COPY --from=final /app/vendor /app/vendor
RUN npm run build

# Switch back to the final stage
FROM final AS production
COPY --from=build /app/public /app/public

RUN sed -i 's/"build": "node vite.js"/"build": "cd var \&\& node ..\/vite.js"/' package.json \
    && sed -i 's/"dev": "node vite.js dev"/"dev": "cd var \&\& node ..\/vite.js dev"/' package.json

COPY .github/docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80
ENTRYPOINT [ "/bin/ash", ".github/docker/entrypoint.sh" ]
CMD [ "supervisord", "-n", "-c", "/etc/supervisord.conf" ]
