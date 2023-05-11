#!/bin/bash

URL='https://github.com/paymenter/paymenter/releases/latest/download/paymenter.tar.gz'

echo "Starting upgrade process..."

if [ "$(php -r 'echo version_compare(PHP_VERSION, "8.1.0");')" -lt 0 ]; then
    echo "Cannot execute self-upgrade process. The minimum required PHP version required is 8.1, you have [$(php -r 'echo PHP_VERSION;')]."
    exit 1
fi

for i in "$@"
do
case $i in
    -u=*|--user=*)
    PERMUSER="${i#*=}"
    shift # past argument=value
    ;;
    -g=*|--group=*)
    PERMGROUP="${i#*=}"
    shift # past argument=value
    ;;
    -i|--install)
    INSTALL=1
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

# Detect the folder permissions.
file=$(pwd)

if [ -t 0 ]; then
    # If $user is set, use that as the user
    if [ -z "$PERMUSER" ]; then
        USER2=$(stat -c '%U' "$file")
        read -p "Your webserver user has been detected as [$USER2]: is this correct? [Y/n]: " -r
        if [[ $REPLY =~ ^[Nn] ]]; then
            read -p "Please enter the name of the user running your webserver process. This varies from system to system, but is generally \"www-data\", \"nginx\", or \"apache\": " -r PERMUSER
        fi
    fi
    
    # If $group is set, use that as the group
    if [ -z "$PERMGROUP" ]; then
        GROUP2=$(stat -c '%G' "$file")
        read -p "Your webserver group has been detected as [$GROUP2]: is this correct? [Y/n]: " -r
        if [[ $REPLY =~ ^[Nn] ]]; then
            read -p "Please enter the name of the group running your webserver process. Normally this is the same as your user: " -r PERMGROUP
        fi
    fi
    
    if [ -z $INSTALL ]; then
        read -p "Are you sure you want to run the upgrade process for your Panel? [y/N]: " -r
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            echo "Upgrade aborted."
            exit 1
        fi
    fi
fi

# Set URL to the default URL if not set.
if [ -z "$URL" ]; then
    DEFAULT_URL="https://github.com/paymenter/paymenter/releases/latest/download/paymenter.tar.gz"
else
    DEFAULT_URL="$URL"
fi


# Download the latest release from GitHub.
echo "\$upgrader> curl -L \"$(printf $DEFAULT_URL)\" | tar -xzv"
curl -L "$(printf $DEFAULT_URL)" | tar -xzv

# Set application down for maintenance.
echo '$upgrader> php artisan down'
php artisan down

# Setup correct permissions on the new files.
echo '$upgrader> chmod -R 755 storage bootstrap/cache'
chmod -R 755 storage bootstrap/cache

# Run the composer install command.
echo '$upgrader> composer install --no-dev --optimize-autoloader'
composer install --no-dev --optimize-autoloader

# Run the database migrations.
echo '$upgrader> php artisan migrate --force'
php artisan migrate --force

# Clear config and view caches.
echo '$upgrader> php artisan config:clear'
php artisan config:clear
echo '$upgrader> php artisan view:clear'
php artisan view:clear

# Remove the old log files.
echo '$upgrader> rm -rf storage/logs/*.log'
rm -rf storage/logs/*.log

# Setup correct permissions on the new files.
echo '$upgrader> chown -R $USER:$GROUP .'
chown -R $PERMUSER:$PERMGROUP .

echo "Upgrade completed."
