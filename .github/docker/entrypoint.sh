#!/bin/ash -e
cd /app

mkdir -p /var/log/supervisord/ /var/log/nginx/ 

## check for .env file and generate app keys if missing
if [ -f /app/var/.env ]; then
  echo "external vars exist."
  rm -rf /app/.env
  
  # Source the file to override environment variables
  set -a
  . /app/var/.env
  set +a

  # Merge local .env with defaults (local wins)
  cat /app/var/.env /app/.env.example > /app/.env
else
  echo "external vars don't exist."
  rm -rf /app/.env
  touch /app/var/.env

  ## manually generate a key because key generate --force fails
  if [ -z $APP_KEY ]; then
     echo -e "Generating key."
     APP_KEY=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)
     echo -e "Generated app key: $APP_KEY"
     echo -e "APP_KEY=$APP_KEY" > /app/var/.env
  else
    echo -e "APP_KEY exists in environment, using that."
    echo -e "APP_KEY=$APP_KEY" > /app/var/.env
  fi

  ln -s /app/var/.env /app/.env
fi

## Symlink themes and extensions
[ -d "/app/var/themes" ] && for theme in /app/var/themes/*; do
  [ -d "$theme" ] || continue
  theme_name=$(basename "$theme")

  # Skip if it's the default theme and it's a symlink pointing to the system default theme
  # This prevents a circular symlink loop where /app/themes/default -> /app/var/themes/default -> /app/themes/default
  if [ "$theme_name" = "default" ] && [ -L "$theme" ] && [ "$(readlink -f "$theme")" = "/app/themes/default" ]; then
    continue
  fi

  rm -rf "/app/themes/$theme_name"
  ln -s "$theme" "/app/themes/$theme_name"
  echo "Linked theme: $theme_name"
done

[ -d "/app/var/extensions" ] && for extension in /app/var/extensions/*/*; do
  [ -d "$extension" ] || continue
  category=$(basename "$(dirname "$extension")")
  mkdir -p "/app/extensions/$category"
  rm -rf "/app/extensions/$category/$(basename "$extension")"
  ln -s "$extension" "/app/extensions/$category/$(basename "$extension")"
  echo "Linked extension: $category/$(basename "$extension")"
done

if [[ -z $DB_PORT ]]; then
  echo -e "DB_PORT not specified, defaulting to 3306"
  DB_PORT=3306
fi

## check for DB up before starting the panel
echo "Checking database status."
until nc -z -v -w30 $DB_HOST $DB_PORT
do
  echo "Waiting for database connection..."
  # wait for 1 seconds before check again
  sleep 1
done

## Symlink vendor directory if it doesn't exist in var
if [ ! -d "/app/var/vendor" ]; then
  echo "Linking vendor directory..."
  ln -s /app/vendor /app/var/vendor
fi

## Symlink node_modules directory if it doesn't exist in var
if [ ! -d "/app/var/node_modules" ]; then
  echo "Linking node_modules directory..."
  ln -s /app/node_modules /app/var/node_modules
fi

## Symlink public directory if it doesn't exist in var
if [ ! -d "/app/var/public" ]; then
  echo "Linking public directory..."
  ln -s /app/public /app/var/public
fi

## Symlink default theme if it doesn't exist in var
if [ ! -d "/app/var/themes/default" ]; then
  echo "Linking default theme..."
  ln -s /app/themes/default /app/var/themes/default
fi

## check if storage symlink exists, if not create it
if [ ! -L /app/public/storage ]; then
  echo -e "Creating storage symlink."
  rm -rf /app/public/storage
  ln -s /app/storage/app/public /app/public/storage
  echo -e "Storage symlink created."
else
  echo -e "Storage symlink already exists."
fi

## set storage permissions to 777
echo -e "Setting storage permissions."
chmod -R 777 /app/storage/*

## make sure the db is set up
echo -e "Migrating and Seeding D.B"
php artisan migrate --seed --force

## start cronjobs for the queue
echo -e "Starting cron jobs."
crond -L /var/log/crond -l 5

echo -e "Starting supervisord."
exec "$@"