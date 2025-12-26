#!/bin/ash -e
cd /app

mkdir -p /var/log/supervisord/ /var/log/nginx/ 

## check for .env file and generate app keys if missing
if [ -f /app/var/.env ]; then
  echo "external vars exist."
  rm -rf /app/.env
  ln -s /app/var/.env /app/.env
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

## check if storage symlink exists, if not create it
if [ ! -L /app/public/storage ]; then
  echo -e "Creating storage symlink."
  rm -rf /app/public/storage
  ln -s /app/storage/app/public /app/public/storage
  echo -e "Storage symlink created."
else
  echo -e "Storage symlink already exists."
fi

## renew default themes and extensions unless PAYMENTER_SKIP_DEFAULT is set
if [ -z "$PAYMENTER_SKIP_DEFAULT" ] || [ "$PAYMENTER_SKIP_DEFAULT" != "true" ]; then
  echo -e "Renewing default themes and extensions..."
  
  # Renew default themes
  if [ -d /app/themes_default ] && [ -d /app/themes ]; then
    echo -e "Renewing themes from defaults..."
    for item in /app/themes_default/*; do
      if [ -e "$item" ]; then
        item_name=$(basename "$item")
        rm -rf "/app/themes/$item_name"
        cp -rp "$item" "/app/themes/"
        echo -e "  Renewed: $item_name"
      fi
    done
  fi
  
  # Renew default extensions - only those that already exist
  if [ -d /app/extensions_default ] && [ -d /app/extensions ]; then
    echo -e "Renewing extensions from defaults..."
    updated_any=0
    for item in /app/extensions_default/*; do
      if [ -e "$item" ]; then
        item_name=$(basename "$item")
        # Only update if extension category exists in /app/extensions
        if [ -d "/app/extensions/$item_name" ]; then
          # Check if any extensions in this category exist
          if [ "$(ls -A /app/extensions/$item_name 2>/dev/null)" ]; then
            # Renew extensions in this category
            for ext_dir in "/app/extensions/$item_name"/*; do
              if [ -d "$ext_dir" ]; then
                ext_name=$(basename "$ext_dir")
                default_ext="/app/extensions_default/$item_name/$ext_name"
                if [ -d "$default_ext" ]; then
                  rm -rf "$ext_dir"
                  cp -rp "$default_ext" "$ext_dir"
                  echo -e "  Renewed: $item_name/$ext_name"
                  updated_any=1
                fi
              fi
            done
          fi
        fi
      fi
    done
    if [ $updated_any -eq 0 ]; then
      echo -e "  No extensions to renew."
    fi
  fi
  
  chown -R nginx:nginx /app/themes /app/extensions
  chmod -R 755 /app/themes /app/extensions
  echo -e "Default themes and extensions renewed."
else
  echo -e "PAYMENTER_SKIP_DEFAULT is set, skipping renewal of default themes and extensions."
fi

## copy default themes if themes directory is empty or doesn't exist
if [ ! -d /app/themes ] || [ -z "$(ls -A /app/themes 2>/dev/null)" ]; then
  echo -e "Themes directory is empty, copying default themes..."
  mkdir -p /app/themes
  if [ -d /app/themes_default ]; then
    cp -rp /app/themes_default/. /app/themes/
    chown -R nginx:nginx /app/themes
    chmod -R 755 /app/themes
    echo -e "Default themes copied."
  fi
else
  echo -e "Themes directory already populated."
fi

## copy default extensions if extensions directory is empty or doesn't exist
if [ ! -d /app/extensions ] || [ -z "$(ls -A /app/extensions 2>/dev/null)" ]; then
  echo -e "Extensions directory is empty, copying default extensions..."
  mkdir -p /app/extensions
  if [ -d /app/extensions_default ]; then
    cp -rp /app/extensions_default/. /app/extensions/
    chown -R nginx:nginx /app/extensions
    chmod -R 755 /app/extensions
    echo -e "Default extensions copied."
  fi
else
  echo -e "Extensions directory already populated."
fi

## set permissions for themes and extensions
echo -e "Setting themes and extensions permissions."
chown -R nginx:nginx /app/themes /app/extensions
chmod -R 755 /app/themes /app/extensions

## set storage permissions to 777 and user nginx:nginx
echo -e "Setting storage permissions."
chmod -R 777 /app/storage/*
chown -R nginx:nginx /app/storage

## make sure the db is set up
echo -e "Migrating and Seeding D.B"
php artisan migrate --seed --force

## start cronjobs for the queue
echo -e "Starting cron jobs."
crond -L /var/log/crond -l 5

echo -e "Starting supervisord."
exec "$@"