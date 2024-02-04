#!/bin/bash

URL='https://github.com/paymenter/paymenter/releases/latest/download/paymenter.tar.gz'

echo "Starting upgrade process..."

if [ "$(php -r 'echo version_compare(PHP_VERSION, "8.1.0");')" -lt 0 ]; then
    echo -e "\x1b[31;1mCannot execute self-upgrade process. The minimum required PHP version required is 8.1, you have [$(php -r 'echo PHP_VERSION;')].\x1b[0m"
    exit 1
fi

# Exit if release URL is empty or underfined
if [[ $URL == "" ]]; then echo -e "\x1b[31;1mRelease URL not defined.\x1b[0m"; exit 1; fi

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
        else
            PERMUSER=$USER2
        fi
    fi
    
    # If $group is set, use that as the group
    if [ -z "$PERMGROUP" ]; then
        GROUP2=$(stat -c '%G' "$file")
        read -p "Your webserver group has been detected as [$GROUP2]: is this correct? [Y/n]: " -r 
        if [[ $REPLY =~ ^[Nn] ]]; then
            read -p "Please enter the name of the group running your webserver process. Normally this is the same as your user: " -r PERMGROUP
        else 
            PERMGROUP=$GROUP2
        fi 
    fi
    
    if [ -z "$INSTALL" ]; then
        read -p "Are you sure you want to run the upgrade process for your Panel? [y/N]: " -r
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            echo "Upgrade aborted."
            exit 1
        fi
    fi
fi

RUN() {
    echo -e "\x1b[34m\$\x1b[34;1mupgrader>\x1b[0m $*"
    "${@}"
}

# Download the latest release from GitHub.
RUN curl -L -o paymenter.tar.gz "$URL"

# Extract the tarball.
RUN tar -xzf paymenter.tar.gz

# Remove the tarball.
RUN rm -f paymenter.tar.gz

# Set application down for maintenance.
RUN php artisan down

# Setup correct permissions on the new files.
RUN chmod -R 755 storage bootstrap/cache

# Run the composer install command.
RUN composer install --no-dev --optimize-autoloader

# Run the database migrations.
RUN php artisan migrate --force --seed

# Link the storage directory.
RUN php artisan storage:link

# Change to default theme.
RUN php artisan p:settings:change-theme default

# Clear config and view caches.
RUN php artisan config:clear
RUN php artisan view:clear

# Remove the old log files.
RUN rm -rf storage/logs/*.log

# Setup correct permissions on the new files.
RUN chown -R "$PERMUSER":"$PERMGROUP" .

php artisan p:check-updates

# Set application up for maintenance.
RUN php artisan up

echo "Upgrade completed."
