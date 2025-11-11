#!/bin/bash

URL='https://github.com/paymenter/paymenter/releases/latest/download/paymenter.tar.gz'

echo "Starting upgrade process..."

# Read config/app.php to check if someone is trying to ugprade to a major version.
if [ -f "config/app.php" ]; then
    if grep -q "marketplace" "config/app.php"; then
        echo -e "\x1b[31;1mCannot execute self-upgrade process. Please follow the upgrade instructions at https://paymenter.org/docs/guides/v0-migration to migrate your V0 to V1\x1b[0m"
        exit 1
    fi
fi

if [ "$(php -r 'echo version_compare(PHP_VERSION, "8.2.0");')" -lt 0 ]; then
    echo -e "\x1b[31;1mCannot execute self-upgrade process. The minimum required PHP version required is 8.2, you have [$(php -r 'echo PHP_VERSION;')].\x1b[0m"
    exit 1
fi

# Exit if release URL is empty or underfined
if [[ $URL == "" ]]; then echo -e "\x1b[31;1mRelease URL not defined.\x1b[0m"; exit 1; fi


# Check if the previous version is 1.2.11 or lower, if so, check if extensions directory contains filament resources (except announcements/affiliates)

if [[ -f "config/app.php" ]]; then
  # Read version from config/app.php
  PREV_VER="$(grep 'version' config/app.php | head -1 | sed -E "s/.*'version'\s*=>\s*'([^']+)'.*/\1/")"

  # Only run checks if version < 1.2.12
  if [[ "$PREV_VER" != *beta* ]] && [[ -n "${PREV_VER:-}" ]] && php -r "exit(version_compare('$PREV_VER','1.2.12','<')?0:1);"; then
    # Check extensions for admin folder
    if [[ -d extensions ]]; then
      found_admin=$(find extensions -type d -name Admin \
        ! -path "*/Announcements/*" \
        ! -path "*/Affiliates/*")
      if [[ -n "$found_admin" ]]; then
        echo -e "\x1b[31;1mExtensions that need to be removed (temporary):\x1b[0m"
        echo "$found_admin"
        echo -e "\x1b[31;1mCannot execute self-upgrade process. Please remove the given extensions, then re-run the upgrade process and add the (updated) extensions again after the upgrade is complete.\x1b[0m"
        exit 1
      fi
    fi
  fi
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

# Set application down for maintenance.
RUN php artisan down

# Download the latest release from GitHub.
RUN curl -L -o paymenter.tar.gz "$URL"

# Delete app folder
RUN rm -rf app bootstrap/cache/*.php

# Extract the tarball.
RUN tar -xzf paymenter.tar.gz

# Remove the tarball.
RUN rm -f paymenter.tar.gz

# Setup correct permissions on the new files.
RUN chmod -R 755 storage bootstrap/cache extensions

# Run the database migrations.
RUN php artisan migrate --force --seed

# Change to default theme.
RUN php artisan app:settings:change theme default

# Clear config and view caches.
RUN php artisan optimize:clear

# Optimize icons
RUN php artisan icons:cache

# Remove the old log files.
RUN rm -rf storage/logs/*.log

# Setup correct permissions on the new files.
RUN chown -R "$PERMUSER":"$PERMGROUP" .

RUN php artisan app:check-for-updates

RUN php artisan queue:restart

# Set application up for maintenance.
RUN php artisan up

echo "Upgrade completed."