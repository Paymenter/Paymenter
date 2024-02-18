#!/bin/bash

if [-d /provisioned]; then
    echo "Already provisioned. Exiting."
    exit 0
fi

dnf install -y epel-release gnupg
dnf config-manager --set-enabled crb
dnf install -y \
    https://dl.fedoraproject.org/pub/epel/epel-release-latest-9.noarch.rpm \
    https://dl.fedoraproject.org/pub/epel/epel-next-release-latest-9.noarch.rpm

dnf install dnf-utils http://rpms.remirepo.net/enterprise/remi-release-9.rpm -y

dnf module enable php:remi-8.3 -y

dnf update -y

dnf install -y \
    curl expect php php-{common,cli,gd,mysql,mbstring,bcmath,xml,fpm,curl,zip} mariadb-server nginx tar unzip git redis

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

systemctl enable --now mariadb
systemctl enable --now nginx
systemctl enable --now redis

mysql \
    -e "CREATE USER 'paymenter'@'127.0.0.1';" \
    -e "CREATE DATABASE paymenter;" \
    -e "GRANT ALL PRIVILEGES ON paymenter.* TO 'paymenter'@'127.0.0.1' WITH GRANT OPTION;" \
    -e "FLUSH PRIVILEGES;"

cd /vagrant

curl -fsSL https://raw.githubusercontent.com/tj/n/master/bin/n | bash -s lts

npm install

cp .env.example .env

php /usr/local/bin/composer install --no-dev --optimize-autoloader

php artisan key:generate --force

mkdir -p /var/www/Paymenter
cp -r /vagrant/* /var/www/Paymenter
cp /vagrant/.env /var/www/Paymenter

cd /var/www/Paymenter

php artisan storage:link
php artisan migrate --force --seed

chown -R nginx:nginx /var/www/Paymenter

# php artisan p:user:create

sed -i 's/user = apache/user = nginx/g' /etc/php-fpm.d/www.conf
sed -i 's/group = apache/group = nginx/g' /etc/php-fpm.d/www.conf

systemctl stop php-fpm
systemctl enable --now php-fpm

cat > ~/Paymenter.conf << EOF
server {
    listen       80;
    listen       [::]:80;
    root         /var/www/Paymenter/public;

    index index.php index.html;
    try_files \$uri \$uri/ /index.php?\$args;

    access_log /var/log/nginx/example_com.access;
    error_log /var/log/nginx/example_com.error;

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php-fpm/www.sock;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }
}
EOF

mv ~/Paymenter.conf /etc/nginx/conf.d/pay.conf

cat > ~/paymenter.service << EOF
[Unit]
Description=Paymenter Queue Worker

[Service]
# On some systems the user and group might be different.
# Some systems use `apache` or `nginx` as the user and group.
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

mv ~/paymenter.service /etc/systemd/system/paymenter.service

systemctl daemon-reload
systemctl enable --now paymenter

(crontab -l 2>/dev/null; echo "* * * * * php /var/www/paymenter/artisan schedule:run >> /dev/null 2>&1") | crontab -

setenforce 0

sed -i 's/SELINUX=enforcing/SELINUX=permissive/g' /etc/selinux/config

systemctl restart nginx

touch /provisioned

echo "Setup complete. You'll want to log into the vagrant session and run \`php artisan p:user:create\` to make a user. See http://localhost:3000 on host machine."