#!/bin/bash

set -e


if [[ $EUID -ne 0 ]]; then
    echo "* This script must be executed with root privileges (sudo)." 1>&2
    exit 1
fi

# Default variables
mysql_db="paymenter"
mysql_user="paymenter"
mysql_password="password"

app_name="Dashboard"
fqdn="demo.paymenter.org"
app_url="https://demo.paymenter.org"
pterodactyl_url=""
pterodactyl_token=""

# installation toggles
configure_letsencrypt=false
email=""

# Visual and input
info() {
    echo "* $1"
}

print_error() {
    COLOR_RED='\033[0;31m'
    COLOR_NC='\033[0m'
    
    echo ""
    echo -e "* ${COLOR_RED}ERROR${COLOR_NC}: $1"
    echo ""
}

required_input() {
    local __resultvar=$1
    local result=''
    
    while [ -z "$result" ]; do
        echo -n "* ${2}"
        read -r result
        
        [ -z "$result" ] && print_error "${3}"
    done
    
    eval "$__resultvar="'$result'""
}

password_input() {
    local __resultvar=$1
    local result=''
    local default="$4"
    
    while [ -z "$result" ]; do
        echo -n "* ${2}"
        
        # modified from https://stackoverflow.com/a/22940001
        while IFS= read -r -s -n1 char; do
            [[ -z $char ]] && {
                printf '\n'
                break
            }                               # ENTER pressed; output \n and break.
            if [[ $char == $'\x7f' ]]; then # backspace was pressed
                # Only if variable is not empty
                if [ -n "$result" ]; then
                    # Remove last char from output variable.
                    [[ -n $result ]] && result=${result%?}
                    # Erase '*' to the left.
                    printf '\b \b'
                fi
            else
                # Add typed char to output variable.
                result+=$char
                # Print '*' in its stead.
                printf '*'
            fi
        done
        [ -z "$result" ] && [ -n "$default" ] && result="$default"
        [ -z "$result" ] && print_error "${3}"
    done
    
    eval "$__resultvar="'$result'""
}

# Pre-installation
detect_distro() {
    if [ -f /etc/os-release ]; then
        # freedesktop.org and systemd
        . /etc/os-release
        OS=$(echo "$ID" | awk '{print tolower($0)}')
        OS_VER=$VERSION_ID
        elif type lsb_release >/dev/null 2>&1; then
        # linuxbase.org
        OS=$(lsb_release -si | awk '{print tolower($0)}')
        OS_VER=$(lsb_release -sr)
        elif [ -f /etc/lsb-release ]; then
        # For some versions of Debian/Ubuntu without lsb_release command
        . /etc/lsb-release
        OS=$(echo "$DISTRIB_ID" | awk '{print tolower($0)}')
        OS_VER=$DISTRIB_RELEASE
        elif [ -f /etc/debian_version ]; then
        # Older Debian/Ubuntu/etc.
        OS="debian"
        OS_VER=$(cat /etc/debian_version)
        elif [ -f /etc/SuSe-release ]; then
        # Older SuSE/etc.
        OS="SuSE"
        OS_VER="?"
        elif [ -f /etc/redhat-release ]; then
        # Older Red Hat, CentOS, etc.
        OS="Red Hat/CentOS"
        OS_VER="?"
    else
        # Fall back to uname, e.g. "Linux <version>", also works for BSD, etc.
        OS=$(uname -s)
        OS_VER=$(uname -r)
    fi
    
    OS=$(echo "$OS" | awk '{print tolower($0)}')
    OS_VER_MAJOR=$(echo "$OS_VER" | cut -d. -f1)
}

check_os_comp() {
    case "$OS" in
        ubuntu)
            [ "$OS_VER_MAJOR" == "20" ] && SUPPORTED=true
        ;;
        *)
            SUPPORTED=false
        ;;
    esac
    
    # exit if not supported
    if [ "$SUPPORTED" == true ]; then
        info "$OS $OS_VER is supported."
    else
        info "$OS $OS_VER is not supported"
        print_error "Unsupported OS"
        exit 1
    fi
}


check_dashboard_present() {
    if [ -d "/var/www/paymenter" ]; then
        print_error "The dashboard is already installed on this machine!"
        exit 1
    fi
}


ask_variables() {
    # MySQL
    read -r -p "* Database name ($mysql_db): " mysql_db
    mysql_db=${mysql_db:-dashboard}
    
    read -r -p "* Database user ($mysql_user): " mysql_user
    mysql_user=${mysql_user:-dashboarduser}
    
    # password input
    rand_pw=$(
        tr -dc 'A-Za-z0-9' </dev/urandom | head -c 64
        echo
    )
    password_input mysql_password "Password (press enter to use randomly generated password): " "MySQL password cannot be empty" "$rand_pw"
    
    # Application details
    read -r -p "* Name of dashboard ($app_name): " app_name
    app_name=${app_name:-Dashboard}
    
    required_input fqdn "FQDN of your new installation: " "FQDN cannot be empty"
    app_url="http://$fqdn"
    
    # Let's Encrypt
    echo -e -n "* Do you want to automatically configure HTTPS using Let's Encrypt? (y/N): "
    read -r confirm_ssl
    
    [[ "$confirm_ssl" =~ [Yy] ]] && configure_letsencrypt=true && app_url="https://$fqdn"
    
    # Ask for email if Let's Encrypt is enabled
    if [ "$configure_letsencrypt" == true ]; then
        required_input email "Email address for Let's Encrypt: " "Email cannot be empty"
    fi
    email=${email:-admin@$fqdn}

    true # last statement of function is function exit value, if above statement evaluates to false then script exits without this
}

confirm() {
    echo -e -n "\n* Initial configuration completed. Continue with installation? (y/N): "
    read -r CONFIRM
    [[ ! "$CONFIRM" =~ [Yy] ]] && print_error "Installation aborted." && exit 1
    true
}

# installation process
install_dependencies() {
    apt update
    apt upgrade -y
    apt -y install software-properties-common curl apt-transport-https ca-certificates gnupg
    LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
    curl -sS https://downloads.mariadb.com/MariaDB/mariadb_repo_setup | sudo bash
    apt update
    apt-add-repository universe
    apt -y install php8.1 php8.1-{common,cli,gd,mysql,mbstring,bcmath,xml,fpm,curl,zip} mariadb-server nginx tar unzip git redis-server
    curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
}

install_controllpanel() {
    mkdir -p /var/www/paymenter
    cd /var/www/paymenter
    
    curl -Lo paymenter.tar.gz https://github.com/paymenter/paymenter/releases/latest/download/paymenter.tar.gz
    tar -xzvf paymenter.tar.gz

    chmod -R 755 storage/* bootstrap/cache/
    
    cp .env.example .env
    COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader
    
    php artisan key:generate --force
    php artisan storage:link
}

create_database() {
    echo "* Performing MySQL queries.."
    
    echo "* Creating MySQL user.."
    mysql -u root -e "CREATE USER '${mysql_user}'@'127.0.0.1' IDENTIFIED BY '${mysql_password}';"
    
    echo "* Creating database.."
    mysql -u root -e "CREATE DATABASE ${mysql_db};"
    
    echo "* Granting privileges.."
    mysql -u root -e "GRANT ALL PRIVILEGES ON ${mysql_db}.* TO '${mysql_user}'@'127.0.0.1' WITH GRANT OPTION;"
    
    echo "* Flushing privileges.."
    mysql -u root -e "FLUSH PRIVILEGES;"
    
    echo "* MySQL database created & configured!"
}

configure() {
    sed -i "s@APP_NAME=Paymenter@APP_NAME=$app_name@g" .env
    sed -i "s@APP_URL=http://localhost@APP_URL=$app_url@g" .env
    
    sed -i "s@DB_DATABASE=paymenter@DB_DATABASE=$mysql_db@g" .env
    sed -i "s@DB_USERNAME=paymenter@DB_USERNAME=$mysql_user@g" .env
    sed -i "s@DB_PASSWORD=@DB_PASSWORD=$mysql_password@g" .env
}

migrate() {
    php artisan migrate
}

create_initial_user() {
    # Create user account
    php artisan p:user:create || true
}

set_permissions() {
    chown -R www-data:www-data /var/www/paymenter/*
}

insert_cronjob() {
    crontab -l | {
        cat
        echo "* * * * * php /var/www/paymenter/artisan schedule:run >> /dev/null 2>&1"
    } | crontab -
}

create_queue_worker() {
    cat <<'EOF' >>/etc/systemd/system/paymenter.service
# paymenter Queue Worker File
# ----------------------------------

[Unit]
Description=paymenter Queue Worker

[Service]
# On some systems the user and group might be different.
# Some systems use $(apache) or $(nginx) as the user and group.
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/paymenter/artisan queue:work --sleep=3 --tries=3

[Install]
WantedBy=multi-user.target
EOF
    systemctl enable --now paymenter.service
}

configure_nginx() {
    cat <<'EOF' >>/etc/nginx/sites-available/paymenter.conf
server {
        listen 80;
        root /var/www/paymenter/public;
        index index.php;
        server_name YOUR.DOMAIN.COM;

        location / {
                try_files $uri $uri/ /index.php?$query_string;
        }

        location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        }

        location ~ /\.ht {
                deny all;
        }
}
EOF
    sed -i "s@YOUR.DOMAIN.COM@$fqdn@g" /etc/nginx/sites-available/paymenter.conf
    ln -s /etc/nginx/sites-available/paymenter.conf /etc/nginx/sites-enabled/paymenter.conf
}

obtain_le_cert() {
    apt-get update
    apt-get install certbot python3-certbot-nginx -y
    certbot --nginx -d $fqdn --non-interactive --agree-tos --redirect --email $email
}

restart_nginx() {
    systemctl restart nginx
}

main() {
    info "Paymenter installation script"
    
    # pre-checks
    #detect_distro
    #check_os_comp
    check_dashboard_present
    
    # ask questions about configuration details
    ask_variables
    confirm
    
    # installation
    install_dependencies
    install_controllpanel
    create_database
    configure
    migrate
    create_initial_user
    set_permissions
    insert_cronjob
    create_queue_worker
    configure_nginx
    [ "$configure_letsencrypt" == true ] && obtain_le_cert
    restart_nginx
}

goodbye() {
    info "Installation completed. Thank you for using this script."
}

# run the installation script
main
goodbye