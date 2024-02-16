sudo dnf install -y epel-release gnupg
sudo dnf config-manager --set-enabled crb
sudo dnf install -y \
    https://dl.fedoraproject.org/pub/epel/epel-release-latest-9.noarch.rpm \
    https://dl.fedoraproject.org/pub/epel/epel-next-release-latest-9.noarch.rpm

sudo dnf install dnf-utils http://rpms.remirepo.net/enterprise/remi-release-9.rpm -y

sudo dnf module enable php:remi-8.3 -y

sudo dnf update -y

sudo dnf install -y \
    curl expect php php-{common,cli,gd,mysql,mbstring,bcmath,xml,fpm,curl,zip} mariadb-server nginx tar unzip git redis

curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

sudo systemctl enable --now mariadb    
sudo systemctl enable --now nginx
sudo systemctl enable --now redis

sudo mysql \
    -e "CREATE USER 'paymenter'@'127.0.0.1';" \
    -e "CREATE DATABASE paymenter;" \
    -e "GRANT ALL PRIVILEGES ON paymenter.* TO 'paymenter'@'127.0.0.1' WITH GRANT OPTION;" \
    -e "FLUSH PRIVILEGES;"

cd /vagrant

cp .env.example .env

composer install --no-dev --optimize-autoloader

php artisan key:generate --force

sudo mkdir -p /var/www/Paymenter
sudo cp -r /vagrant/* /var/www/Paymenter
sudo cp /vagrant/.env /var/www/Paymenter

cd /var/www/Paymenter

sudo php artisan storage:link
sudo php artisan migrate --force --seed

sudo chown -R nginx:nginx /var/www/Paymenter

# php artisan p:user:create

sudo sed -i 's/user = apache/user = nginx/g' /etc/php-fpm.d/www.conf
sudo sed -i 's/group = apache/group = nginx/g' /etc/php-fpm.d/www.conf

sudo systemctl stop php-fpm
sudo systemctl enable --now php-fpm

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

sudo mv ~/Paymenter.conf /etc/nginx/conf.d/pay.conf
rm ~/Paymenter.conf

sudo setenforce 0

sed -i 's/SELINUX=enforcing/SELINUX=permissive/g' /etc/selinux/config

sudo systemctl restart nginx

echo "Setup complete. You'll want to log into the vagrant session and run \`php artisan p:user:create\` to make a user. See http://localhost:3000 on host machine."