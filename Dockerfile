# Stage 1:
# Build the actual container with all of the needed PHP dependencies that will run the application.
FROM --platform=$TARGETOS/$TARGETARCH php:8.3-fpm-alpine AS final
WORKDIR /app

COPY . ./
RUN apk add --no-cache --update ca-certificates dcron curl git supervisor tar unzip nginx libpng-dev libxml2-dev libzip-dev icu-dev autoconf make g++ gcc libc-dev linux-headers gmp-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install bcmath gd pdo_mysql zip intl sockets gmp \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del autoconf make g++ gcc libc-dev \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && cp .env.example .env \
    && chmod 777 -R bootstrap storage/* \
    && composer install --no-dev --optimize-autoloader \
    && rm -rf .env bootstrap/cache/*.php \
    && chown -R nginx:nginx .

RUN rm /usr/local/etc/php-fpm.conf \
    && echo "* * * * * /usr/local/bin/php /app/artisan schedule:run >> /dev/null 2>&1" >> /var/spool/cron/crontabs/root \
    && mkdir -p /var/run/php /var/run/nginx

FROM --platform=$TARGETOS/$TARGETARCH node:22-alpine AS build
WORKDIR /app
COPY . ./
COPY --from=final /app/vendor /app/vendor
RUN npm install \
    && npm run build

# Switch back to the final stage
FROM final AS production
COPY --from=build /app/public /app/public

COPY .github/docker/default.conf /etc/nginx/http.d/default.conf
COPY .github/docker/www.conf /usr/local/etc/php-fpm.conf
COPY .github/docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80
ENTRYPOINT [ "/bin/ash", ".github/docker/entrypoint.sh" ]
CMD [ "supervisord", "-n", "-c", "/etc/supervisord.conf" ]
