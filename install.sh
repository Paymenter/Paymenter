
#!/bin/bash

GREEN='\033[0;32m'
RED='\033[0;31m'
WHITE='\033[1;37m'
YELLOW='\033[1;33m'
NC='\033[0m'
RESET='\e[0m'
BLUE='\033[34m'

checkmark="${WHITE}[${GREEN}\xE2\x9C\x93${WHITE}]"
cross="${WHITE}[${RED}\xE2\x9C\x97${WHITE}]"
ask="${WHITE}[${YELLOW}?${WHITE}]"
 
if [[ $EUID -ne 0 ]]; then
   echo -e "${cross} This script must be run as root."
   exit 1
fi
check_distribution() {
    supported_distributions=("Ubuntu 18" "Ubuntu 20" "Ubuntu 22" "CentOS Linux 7" "CentOS Linux 8" "Debian GNU/Linux 10" "Debian GNU/Linux 11")
    if [[ -f /etc/os-release ]]; then
        source /etc/os-release
        for dist in "${supported_distributions[@]}"; do
            if [[ "$PRETTY_NAME" == *"$dist"* ]]; then
                return
            fi
        done
    fi
    echo -e "${cross} This is an unsupported Linux distribution/version."
    exit 1
}

 
install() {
    echo -e "${checkmark} Installing dependencies"
    apt update > /dev/null 2>&1 && apt upgrade > /dev/null 2>&1
    apt -y install software-properties-common curl apt-transport-https ca-certificates gnupg > /dev/null 2>&1
    LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php > /dev/null 2>&1
    if [ "$(lsb_release -si)" != "Ubuntu" ] || [ "$(lsb_release -sr)" != "22.04" ]; then
    if ! dpkg -l | grep -q mariadb-server; then
        curl -sS https://downloads.mariadb.com/MariaDB/mariadb_repo_setup | bash > /dev/null 2>&1
    fi
    fi


    apt update > /dev/null 2>&1
    if [ "$(lsb_release -si)" != "Ubuntu" ] || [ "$(lsb_release -sr)" = "18.04" ]; then
    apt-add-repository universe > /dev/null 2>&1
    fi
    apt -y install php8.1 php8.1-{common,cli,gd,mysql,mbstring,bcmath,xml,fpm,curl,zip} mariadb-server nginx tar unzip git redis-server > /dev/null 2>&1
    curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer > /dev/null 2>&1
    echo -e "${checkmark} Downloading paymenter"
    if [ -d "/var/www/paymenter" ]; then
      rm -rf /var/www/paymenter/
    fi
    mkdir -p /var/www/paymenter
    curl -Lo /var/www/paymenter/paymenter.tar.gz https://github.com/paymenter/paymenter/releases/latest/download/paymenter.tar.gz > /dev/null 2>&1
    tar -xzvf /var/www/paymenter/paymenter.tar.gz -C /var/www/paymenter/ > /dev/null 2>&1
    chmod -R 755 /var/www/paymenter/storage/* /var/www/paymenter/bootstrap/cache/
    echo -e "${checkmark} Setup environment" 
    while true; do echo -e -n "${ask} Enter Paymenter Domain or IP (Include https://): " && read app_url && [[ $app_url =~ ^(http:\/\/localhost|http:\/\/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+|https?:\/\/(localhost|[0-9]{3}\.[0-9]{3}\.[0-9]{3}|[a-zA-Z0-9.-]+[.][a-zA-Z]+))$ ]] && { echo ""; break; } || echo "Invalid app_url format. It should be a valid HTTP or HTTPS URL."; done
    while true; do echo -e -n "${ask} Enter admin user email: " && read email && [[ "$email" =~ ^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$ ]] && break; echo "Invalid email address format. Please enter a valid email address."; done
    echo -e "${ask} Enter admin user password: (hidden input)" && read -s password
    echo -e -n "${ask} Enter admin username: " && read username
    echo -e "$email\n$password\n$username\nadmin" | php /var/www/paymenter/artisan p:user:create  > /dev/null 2>&1
    env_file="/var/www/paymenter/.env"
    export email=email
    export server_name=app_url
    sed -i "s|APP_URL=http://localhost|APP_URL=$app_url|g" /var/www/paymenter/.env
    sed -i "s|DB_PASSWORD=|DB_PASSWORD=$DB_PASSWORD|" /var/www/paymenter/.env
    password=$(openssl rand -base64 12)
    export DB_PASSWORD="$password"
    mariadb --execute="DROP USER IF EXISTS 'paymenter'@'127.0.0.1'; DROP DATABASE IF EXISTS paymenter;"
    mariadb --execute="CREATE USER 'paymenter'@'127.0.0.1' IDENTIFIED BY '$password'; CREATE DATABASE paymenter; GRANT ALL PRIVILEGES ON paymenter.* TO 'paymenter'@'127.0.0.1' WITH GRANT OPTION;"
    cp /var/www/paymenter/.env.example /var/www/paymenter/.env > /dev/null 2>&1
    export COMPOSER_ALLOW_SUPERUSER=1
    composer install -d /var/www/paymenter/ --no-dev --optimize-autoloader > /dev/null 2>&1
    php /var/www/paymenter/artisan key:generate --force > /dev/null 2>&1
    php /var/www/paymenter/artisan migrate --force --seed > /dev/null 2>&1
    if [[ $app_url == http://* ]]; then
        domain=$(echo $app_url | sed 's/^http:\/\///')
        rm -f /etc/nginx/sites-available/paymenter.conf
        cat > /etc/nginx/sites-available/paymenter.conf <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name $domain;
    root /var/www/paymenter/public;

    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
}
EOF
    elif [[ $app_url == https://* ]]; then
        domain=$(echo $app_url | sed 's/^https:\/\///')
        rm -f /etc/nginx/sites-available/paymenter.conf
        cat > /etc/nginx/sites-available/paymenter.conf <<EOF
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name $domain;
    root /var/www/paymenter/public;

    index index.php;

    ssl_certificate /etc/letsencrypt/live/$domain/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$domain/privkey.pem;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
}
EOF
        systemctl stop nginx
        certbot certonly --standalone -d $domain --non-interactive --agree-tos --register-unsafely-without-email
    fi
    chown -R www-data:www-data /var/www/paymenter/*
    rm -f /etc/nginx/sites-enabled/paymenter.conf
    ln -s /etc/nginx/sites-available/paymenter.conf /etc/nginx/sites-enabled/paymenter.conf
    systemctl restart nginx
    apt -y install cron > /dev/null 2>&1
    (crontab -l 2>/dev/null ; echo "* * * * * php /var/www/paymenter/artisan schedule:run >> /dev/null 2>&1") | crontab - > /dev/null 2>&1
    rm -f /etc/systemd/system/paymenter.service
    echo "[Unit]
Description=Paymenter Queue Worker
 
[Service]
 
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/paymenter/artisan queue:work
StartLimitInterval=180
StartLimitBurst=30
RestartSec=5s
 
[Install]
WantedBy=multi-user.target
    " | tee /etc/systemd/system/paymenter.service > /dev/null
    systemctl enable --now paymenter.service > /dev/null 2>&1
 
}
install_debug() {
    set -x
    echo -e "${checkmark} Installing dependencies"
    apt update && apt upgrade
    apt -y install software-properties-common curl apt-transport-https ca-certificates gnupg
    LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
    if [ "$(lsb_release -si)" != "Ubuntu" ] || [ "$(lsb_release -sr)" != "22.04" ]; then
    if ! dpkg -l | grep -q mariadb-server; then
        curl -sS https://downloads.mariadb.com/MariaDB/mariadb_repo_setup | sudo bash
    fi
    fi


    apt update
    if [ "$(lsb_release -si)" != "Ubuntu" ] || [ "$(lsb_release -sr)" = "18.04" ]; then
    apt-add-repository universe
    fi
    apt -y install php8.1 php8.1-{common,cli,gd,mysql,mbstring,bcmath,xml,fpm,curl,zip} mariadb-server nginx tar unzip git redis-server
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    echo -e "${checkmark} Downloading paymenter"
    if [ -d "/var/www/paymenter" ]; then
      rm -rf /var/www/paymenter/
    fi
    mkdir -p /var/www/paymenter
    curl -Lo /var/www/paymenter/paymenter.tar.gz https://github.com/paymenter/paymenter/releases/latest/download/paymenter.tar.gz
    tar -xzvf /var/www/paymenter/paymenter.tar.gz -C /var/www/paymenter/
    chmod -R 755 /var/www/paymenter/storage/* /var/www/paymenter/bootstrap/cache/
    set -x
    echo -e "${checkmark} Setup environment" 
    while true; do echo -e -n "${ask} Enter Paymenter Domain or IP (Include https://): " && read app_url && [[ $app_url =~ ^(http:\/\/localhost|http:\/\/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+|https?:\/\/(localhost|[0-9]{3}\.[0-9]{3}\.[0-9]{3}|[a-zA-Z0-9.-]+[.][a-zA-Z]+))$ ]] && { echo ""; break; } || echo "Invalid app_url format. It should be a valid HTTP or HTTPS URL."; done
    while true; do echo -e -n "${ask} Enter admin user email: " && read email && [[ "$email" =~ ^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$ ]] && break; echo "Invalid email address format. Please enter a valid email address."; done
    echo -e "${ask} Enter admin user password: (hidden input)" && read -s password
    echo -e -n "${ask} Enter admin username: " && read username
    set -x
    echo -e "$email\n$password\n$username\nadmin" | php /var/www/paymenter/artisan p:user:create
    env_file="/var/www/paymenter/.env"
    export email=email
    export server_name=app_url
    sed -i "s|APP_URL=http://localhost|APP_URL=$app_url|g" /var/www/paymenter/.env
    sed -i "s|DB_PASSWORD=|DB_PASSWORD=$DB_PASSWORD|" /var/www/paymenter/.env
    password=$(openssl rand -base64 12)
    export DB_PASSWORD="$password"
    mariadb --execute="DROP USER IF EXISTS 'paymenter'@'127.0.0.1'; DROP DATABASE IF EXISTS paymenter;"
    mariadb --execute="CREATE USER 'paymenter'@'127.0.0.1' IDENTIFIED BY '$password'; CREATE DATABASE paymenter; GRANT ALL PRIVILEGES ON paymenter.* TO 'paymenter'@'127.0.0.1' WITH GRANT OPTION;"
    cp /var/www/paymenter/.env.example /var/www/paymenter/.env
    export COMPOSER_ALLOW_SUPERUSER=1
    composer install -d /var/www/paymenter/ --no-dev --optimize-autoloader
    php /var/www/paymenter/artisan key:generate --force
    php /var/www/paymenter/artisan migrate --force --seed 
    if [[ $app_url == http://* ]]; then
        domain=$(echo $app_url | sed 's/^http:\/\///')
        rm -f /etc/nginx/sites-available/paymenter.conf
        cat > /etc/nginx/sites-available/paymenter.conf <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name $domain;
    root /var/www/paymenter/public;

    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
}
EOF
    elif [[ $app_url == https://* ]]; then
        domain=$(echo $app_url | sed 's/^https:\/\///')
        rm -f /etc/nginx/sites-available/paymenter.conf
        cat > /etc/nginx/sites-available/paymenter.conf <<EOF
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name $domain;
    root /var/www/paymenter/public;

    index index.php;

    ssl_certificate /etc/letsencrypt/live/$domain/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$domain/privkey.pem;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
}
EOF
        systemctl stop nginx
        certbot certonly --standalone -d $domain --non-interactive --agree-tos --register-unsafely-without-email
    fi
    chown -R www-data:www-data /var/www/paymenter/*
    rm -f /etc/nginx/sites-enabled/paymenter.conf
    ln -s /etc/nginx/sites-available/paymenter.conf /etc/nginx/sites-enabled/paymenter.conf
    systemctl restart nginx
    apt -y install cron
    (crontab -l 2>/dev/null ; echo "* * * * * php /var/www/paymenter/artisan schedule:run >> /dev/null 2>&1") | crontab - > /dev/null 2>&1
    rm -f /etc/systemd/system/paymenter.service
    echo "[Unit]
Description=Paymenter Queue Worker
 
[Service]
 
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/paymenter/artisan queue:work
StartLimitInterval=180
StartLimitBurst=30
RestartSec=5s
 
[Install]
WantedBy=multi-user.target
    " | tee /etc/systemd/system/paymenter.service
    systemctl enable --now paymenter.service
    set -x
 
}

main() {
  echo -e "${BLUE}   ____                                   __           "
  echo "  / __ \____ ___  ______ ___  ___  ____  / /____  _____"
  echo " / /_/ / __ \`/ / / / __ \`__ \/ _ \/ __ \/ __/ _ \/ ___/"
  echo " / ____/ /_/ / /_/ / / / / / /  __/ / / / /_/  __/ /    "
  echo -e "/_/    \__,_/\__, /_/ /_/ /_/\___/_/ /_/\__/\___/_/     "
  echo -e "            /____/                                      ${RESET}"
  echo
  echo "Paymenter is an open-source webshop solution for hosting companies. It's developed to provide an easier way to manage your hosting company."
  echo 
  echo "Available commands:"
  echo "  install   - Install Paymenter"
  echo "  uninstall - Completely uninstall Paymenter"
}
uninstall(){
  echo -e "${ask} Are you sure you want to uninstall Paymenter? (y/N): "
  read -r confirm
  if [[ "$confirm" =~ ^[Yy]$ ]]; then
    echo -e "${checkmark} Uninstalling Paymenter..."
    mariadb --execute="DROP USER IF EXISTS 'paymenter'@'127.0.0.1'; DROP DATABASE IF EXISTS paymenter;"
    rm -rf /var/www/paymenter/
    rm -f /etc/nginx/sites-available/paymenter.conf
    rm -f /etc/nginx/sites-enabled/paymenter.conf
    (crontab -l | grep -v "* * * * * php /var/www/paymenter/artisan schedule:run >> /dev/null 2>&1") | crontab -
    systemctl stop paymenter.service
    rm -f /etc/systemd/system/paymenter.service
    
    echo -e "${checkmark} Paymenter has been successfully uninstalled."
  else
    echo -e "${cross} Uninstallation canceled."
  fi
}
while [[ "$#" -gt 0 ]]; do
    case $1 in
        -d|--debug)
            debug=1
            ;;
        install)
            operation="install"
            ;;
        uninstall)
            operation="uninstall"
            ;;
        *)
            echo "Unknown parameter: $1"
            exit 1
            ;;
    esac
    shift
done

if [[ "$operation" == "install" ]]; then
    check_distribution
    echo -e "${WHITE}Welcome to the ${BLUE}Paymenter ${WHITE}installation script${RESET}"
    
    if [ -d "/var/www/paymenter" ]; then
        echo -e "${ask} Paymenter is already installed in /var/www/paymenter. Do you want to reinstall it? (y/N): " && read reinstall
        if [[ ! "$reinstall" =~ ^[Yy]$ ]]; then
            echo -e "${cross} Reinstallation of Paymenter canceled."
            exit 0
        fi
    fi

    echo -e "${ask} Are you sure you want to proceed with the installation (y/N): " && read confirm

    if [[ "$confirm" =~ ^[Yy]$ ]]; then
        if [[ -n "$debug" ]]; then
            install_debug
        else
            install
        fi
    else
        echo -e "${cross} Installation of Paymenter canceled."
    fi

elif [[ "$operation" == "uninstall" ]]; then
    uninstall
else
    main
fi
