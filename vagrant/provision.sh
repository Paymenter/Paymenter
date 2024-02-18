#!/bin/bash

# Disable SELinux
setenforce 0
sed -i 's/SELINUX=enforcing/SELINUX=disabled/' /etc/selinux/config

# Update the system
dnf update -y

# Install the EPEL and Remi repositories
dnf -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-9.noarch.rpm
dnf -y install https://rpms.remirepo.net/enterprise/remi-release-9.rpm
dnf -y config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo

# Install Redis
dnf -y install redis
systemctl enable --now redis

# Install PHP 8.2 and configure PHP-FPM
dnf -y module enable php:remi-8.2
dnf -y install php php-{cli,gd,mysqlnd,mbstring,bcmath,xml,fpm,curl,zip,posix}
cat >/etc/php-fpm.d/paymenter.conf <<EOF
[paymenter]
user = vagrant
group = vagrant
listen = /run/php-fpm/paymenter.sock
listen.owner = vagrant
listen.group = vagrant
pm = ondemand
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 10
pm.process_idle_timeout = 10s
pm.max_requests = 500
chdir = /
EOF

systemctl enable --now php-fpm

# Install Nginx and the configuration for Paymenter
dnf -y install nginx
sed -i 's/user nginx;/user vagrant;/' /etc/nginx/nginx.conf
cat >/etc/nginx/conf.d/paymenter.conf <<EOF
server {
    listen 3000;
    server_name localhost;

    root /var/www/paymenter/public;
    index index.html index.htm index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    access_log off;
    error_log  /var/log/nginx/paymenter.app-error.log error;

    # allow larger file uploads and longer script runtimes
    client_max_body_size 100m;
    client_body_timeout 120s;

    sendfile off;

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/run/php-fpm/paymenter.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param PHP_VALUE "upload_max_filesize = 100M \n post_max_size=100M";
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param HTTP_PROXY "";
        fastcgi_intercept_errors off;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

systemctl enable --now nginx

# Install MariaDB
dnf -y install mariadb-server
systemctl enable --now mariadb

# Install Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enter the web directory
cd /var/www/paymenter || exit
chmod -R 755 storage/* bootstrap/cache/

# Create the database and user for Paymenter
mysql -u root -e "CREATE DATABASE paymenter;" \
    -e "CREATE USER 'paymenter'@'localhost' IDENTIFIED BY 'password';" \
    -e "GRANT ALL PRIVILEGES ON paymenter.* TO 'paymenter'@'localhost' WITH GRANT OPTION;" \
    -e "FLUSH PRIVILEGES;"

# Initialize Paymenter
cp .env.example .env
php /usr/local/bin/composer install --no-dev --optimize-autoloader

# PHP Artisan commands
php artisan key:generate --force
php artisan storage:link

# Update the .env file
sed -i 's/^DB_DATABASE=.*/DB_DATABASE=paymenter/' .env
sed -i 's/^DB_USERNAME=.*/DB_USERNAME=paymenter/' .env
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=password/' .env

# Migrate the database
php artisan migrate --force --seed

# Create the first user by walking through the installation
dnf -y install expect
chmod +x vagrant/create_user.exp
./vagrant/create_user.exp

# Install the crontab
(
    crontab -l 2>/dev/null
    echo "* * * * * php /var/www/paymenter/artisan schedule:run >> /dev/null 2>&1"
) | crontab -

# Install the queue worker
cat >/etc/systemd/system/paymenter.service <<EOF
[Unit]
Description=Paymenter Queue Worker

[Service]
User=vagrant
Group=vagrant
Restart=always
ExecStart=/usr/bin/php /var/www/paymenter/artisan queue:work
StartLimitInterval=180
StartLimitBurst=30
RestartSec=5s

[Install]
WantedBy=multi-user.target
EOF

systemctl enable --now paymenter
