#!/bin/bash

full=0

while (( "$#" )); do
  case "$1" in
    --full)
      full=1
      shift
      ;;
    *)
      echo "Error: Invalid argument"
      exit 1
      ;;
  esac
done

cd /vagrant

cp .env.example .env

mkdir -p /var/www/Paymenter
if [ $full -eq 1 ]; then
    php /usr/local/bin/composer install --no-dev --optimize-autoloader
fi
cp -r /vagrant/* /var/www/Paymenter
cp /vagrant/.env /var/www/Paymenter

chown -R nginx:nginx /var/www/Paymenter