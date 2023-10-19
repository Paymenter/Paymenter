#!/bin/bash

#################
# This is a script to install paymenter, an open-source webshop solution
# Made by david1117dev :)
#################

source <(curl -s https://raw.githubusercontent.com/david1117dev/NextLib/main/lib.sh)
runasroot
if [ -d /var/www/paymenter/ ]; then
    read -p $'\033[1;33m[WARN]\033[0m \033[97mThis operation may delete data in /var/www/paymenter/. Continue? (y/n):\033[0m ' confirm
    if [[ $confirm != "y" ]]; then
        fatal "Cancelled by user."
        exit 1
    fi
fi
supported_distributions=("Ubuntu 18.04" "Ubuntu 20.04" "Ubuntu 22.04" "CentOS 7" "CentOS 8" "Debian 10" "Debian 11")
info "Checking supported operating system..."
check_distribution "${supported_distributions[@]}"
info "Installing required dependencies..."
install "software-properties-common,curl,apt-transport-https,ca-certificates,gnupg"
apt-add-repository -y ppa:ondrej/php > "$OUTPUT_TARGET"

# Additional steps for Ubuntu 22.04
if [[ "$PRETTY_NAME" == *"Ubuntu 22.04"* ]]; then
    info "Skipping MariaDB repo setup on Ubuntu 22.04."
else
    info "Setting up MariaDB repository..."
    curl -sS https://downloads.mariadb.com/MariaDB/mariadb_repo_setup | sudo bash
fi
apt-get update > "$OUTPUT_TARGET"
if [[ "$PRETTY_NAME" == *"Ubuntu 18.04"* ]]; then
    info "Adding universe repository for Ubuntu 18.04..."
    apt-add-repository universe  > "$OUTPUT_TARGET"# Add universe repository for Ubuntu 18.04
fi

info "Installing required packages..."
install "php8.1,php8.1-common,php8.1-cli,php8.1-gd,php8.1-mysql,php8.1-mbstring,php8.1-bcmath,php8.1-xml,php8.1-fpm,php8.1-curl,php8.1-zip,mariadb-server,nginx,tar,unzip,git,redis-server,certbot,python3-certbot-nginx"
info "Installing Composer..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer > /dev/null
info "Installing Paymenter..."
mkdir -p /var/www/paymenter
curl --silent -Lo /var/www/paymenter/paymenter.tar.gz https://github.com/paymenter/paymenter/releases/latest/download/paymenter.tar.gz > /dev/null
tar -xzvf /var/www/paymenter/paymenter.tar.gz -C /var/www/paymenter/ > "$OUTPUT_TARGET"
chmod -R 755 /var/www/paymenter/storage/* /var/www/paymenter/bootstrap/cache/
cp /var/www/paymenter/.env.example /var/www/paymenter/.env
export COMPOSER_ALLOW_SUPERUSER=1
composer install -q --working-dir /var/www/paymenter/ --no-dev --optimize-autoloader > "$OUTPUT_TARGET"
php /var/www/paymenter/artisan key:generate --force > "$OUTPUT_TARGET"
php /var/www/paymenter/artisan storage:link > "$OUTPUT_TARGET"
question "Enter Paymenter url (with https://)" app_url url
question "Enter admin user email" email email
question "Enter admin user name" name 
question "Enter admin user password" password password
mariadb_manage usercreate paymenter random
mariadb_manage dbcreate paymenter > "$OUTPUT_TARGET"
mariadb --execute "GRANT ALL PRIVILEGES ON paymenter.* TO 'paymenter'@'127.0.0.1' WITH GRANT OPTION;"
replace /var/www/paymenter/.env "DB_PASSWORD=" "DB_PASSWORD=${PASS}" > "$OUTPUT_TARGET"
php /var/www/paymenter/artisan migrate --force --seed > "$OUTPUT_TARGET"
echo -e "$email\n$password\n$username\nadmin" | php /var/www/paymenter/artisan p:user:create > "$OUTPUT_TARGET"
rm -f /etc/nginx/sites-available/default
rm -f /etc/nginx/sites-enabled/default
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
certbot certonly --standalone -d $domain --non-interactive --agree-tos --register-unsafely-without-email
fi
chown -R www-data:www-data /var/www/paymenter/*
rm -f /etc/nginx/sites-enabled/paymenter.conf
ln -s /etc/nginx/sites-available/paymenter.conf /etc/nginx/sites-enabled/paymenter.conf
systemctl restart nginx
(crontab -l ; echo "* * * * * php /var/www/paymenter/artisan schedule:run >> /dev/null 2>&1") | crontab - > "$OUTPUT_TARGET"

# Create a Queue Worker systemd service
cat > /etc/systemd/system/paymenter.service <<EOF
[Unit]
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
EOF

systemctl enable --now paymenter.service > "$OUTPUT_TARGET"
info "Your panel should be accesible at ${domain}"
