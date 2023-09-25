#!/bin/bash

URL='https://github.com/paymenter/paymenter/releases/latest/download/paymenter.tar.gz'
INSTALL=0

echo "Starting the upgrade process..."

# Check PHP version
if [ "$(php -r 'echo version_compare(PHP_VERSION, "8.1.0");')" -lt 0 ]; then
    echo "Cannot execute self-upgrade process. The minimum required PHP version is 8.1, but you have PHP $(php -r 'echo PHP_VERSION;')."
    exit 1
fi

# Parse command line arguments
for i in "$@"; do
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
            shift # past argument
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

# Detect folder permissions
file=$(pwd)

if [ -t 0 ]; then
    # Auto-detect user and group
    if [ -z "$PERMUSER" ]; then
        USER2=$(stat -c '%U' "$file")
        read -p "Your webserver user has been detected as [$USER2]. Is this correct? [Y/n]: " -r
        if [[ ! $REPLY =~ ^[Nn] ]]; then
            PERMUSER=$USER2
        else
            read -p "Please enter the name of the user running your webserver process (e.g., 'www-data', 'nginx', or 'apache'): " -r PERMUSER
        fi
    fi

    if [ -z "$PERMGROUP" ]; then
        GROUP2=$(stat -c '%G' "$file")
        read -p "Your webserver group has been detected as [$GROUP2]. Is this correct? [Y/n]: " -r
        if [[ ! $REPLY =~ ^[Nn] ]]; then
            PERMGROUP=$GROUP2
        else
            read -p "Please enter the name of the group running your webserver process (usually the same as your user): " -r PERMGROUP
        fi
    fi

    if [ $INSTALL -eq 0 ]; then
        read -p "Are you sure you want to run the upgrade process for your Panel? [y/N]: " -r
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            echo "Upgrade aborted."
            exit 1
        fi
    fi
fi

# Set URL to the default URL if not set
if [ -z "$URL" ]; then
    URL="https://github.com/paymenter/paymenter/releases/latest/download/paymenter.tar.gz"
fi

# Download the latest release from GitHub
echo "\$upgrader> curl -L \"$URL\" | tar -xzv"
curl -L "$URL" | tar -xzv

# Set application down for maintenance
echo '$upgrader> php artisan down'
php artisan down

# Setup correct permissions on the new files
echo '$upgrader> chmod -R 755 storage bootstrap/cache'
chmod -R 755 storage bootstrap/cache

# Run the composer install command
echo '$upgrader> composer install --no-dev --optimize-autoloader'
composer install --no-dev --optimize-autoloader

# Run the database migrations
echo '$upgrader> php artisan migrate --force --seed'
php artisan migrate --force --seed

# Link the storage directory
echo '$upgrader> php artisan storage:link'
php artisan storage:link

# Clear config and view caches
echo '$upgrader> php artisan config:clear'
php artisan config:clear
echo '$upgrader> php artisan view:clear'
php artisan view:clear

# Remove the old log files
echo '$upgrader> rm -rf storage/logs/*.log'
rm -rf storage/logs/*.log

# Setup correct permissions on the new files
echo '$upgrader> chown -R '$PERMUSER':'$PERMGROUP '.'
chown -R $PERMUSER:$PERMGROUP .

# Set application up for maintenance
echo '$upgrader> php artisan up'
php artisan up

echo "Upgrade completed."
