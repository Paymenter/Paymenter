#!/bin/bash

# Update the system
# Called like this: update.sh --user=USER --group=GROUP --url=URL

# Get the arguments
for i in "$@"
do
case $i in
    -u=*|--user=*)
    USER="${i#*=}"
    shift # past argument=value
    ;;
    -g=*|--group=*)
    GROUP="${i#*=}"
    shift # past argument=value
    ;;
    -r=*|--url=*)
    URL="${i#*=}"
    shift # past argument=value
    ;;
    *)
            # unknown option
    ;;
esac
done

# If not set, set the default values
if [ -z "${USER}" ]; then
    USER="www-data"
fi

if [ -z "${GROUP}" ]; then
    GROUP="www-data"
fi

if [ -z "${URL}" ]; then
    URL="https://github.com/paymenter/paymenter/releases/latest/download/paymenter.tar.gz"
fi


# Update the system
curl -L "$URL" | tar -xzv 

# Put the site in maintenance mode
php artisan down

# Update composer
composer install --no-dev --optimize-autoloader

# Migrate the database
php artisan migrate --force

# Clear the cache
php artisan cache:clear
php artisan view:clear

# Remove log of today
rm -rf storage/logs/laravel-$(date +%Y-%m-%d).log

# Change the owner of the site
chown -R $USER:$GROUP .

# Put the site back online
php artisan up


