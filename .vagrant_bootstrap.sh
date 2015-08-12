#!/usr/bin/env bash

## Configuration ##
WEBSERVER="nginx" # apache2 or nginx

TEMPLATE_DIR="/vagrant/templates" # dir where all template files are.

SYMFONY_APP_DIR="/vagrant/app"
SYMFONY_CONFIG_DIR="$SYMFONY_APP_DIR/config"

VAGRANT_USER="vagrant"
SYMLINK_NAME=$(hostname)

# Just for cosmetic purposes
SSH_PORT=2222
## End Of Configuration ##


PROVISION_START=`date +%s`
echo "Starting to provision: $(date)"

cd /vagrant
HTTPS_PORT=`grep -o 'config.vm.network.*guest: 443.*' Vagrantfile | sed -E 's/.*guest: 443, host: ([0-9]+).*/\1/'`

echo "Appending to hosts file"
echo '127.0.0.1 korobi.dev' >> /etc/hosts

echo "Adding ruby PPA.."
apt-add-repository ppa:brightbox/ruby-ng -y

echo "Adding key for MongoDB.."
apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
echo "deb http://repo.mongodb.org/apt/ubuntu "$(lsb_release -sc)"/mongodb-org/3.0 multiverse" | tee /etc/apt/sources.list.d/mongodb-org-3.0.list

echo "Adding PPA for PHP.."
apt-add-repository ppa:ondrej/php5-5.6

echo "Updating packages.."
apt-get update -y > ~/provision-apt-get-update.log && apt-get upgrade -y > ~/provision-apt-get-upgrade.log

if [[ $WEBSERVER == "apache2" ]]; then
    EXTRA_PACKAGES=""
elif [[ $WEBSERVER == "nginx" ]]; then
    EXTRA_PACKAGES="php5-fpm nginx-core"
else
    EXTRA_PACKAGES=""
fi

apt-get build-dep $WEBSERVER -y > ~/provision-apt-get-builddep.log
apt-get install mongodb-org php5 php5-cli php5-curl php5-mcrypt php5-mongo openssl ruby2.1 nodejs nodejs-legacy npm git $EXTRA_PACKAGES $WEBSERVER -y > ~/provision-apt-get-install.log
echo "Done. apt logs are in $HOME"

echo "Installing composer.."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/bin/composer

echo "Getting PHPUnit.."
wget --quiet https://phar.phpunit.de/phpunit.phar
mv phpunit.phar /usr/bin/phpunit

echo "Installing bundler & ruby project dependencies.."
gem install bundler sass
sudo -i -u $VAGRANT_USER bundle install --path=/vagrant --gemfile=/vagrant/Gemfile


echo "Installing nodejs project dependencies.."
npm install -g uglify-js

echo "Symlinking /vagrant to /home/vagrant/$SYMLINK_NAME"
ln -s /vagrant /home/vagrant/$SYMLINK_NAME

echo "Copying params.yml.."
cp "$TEMPLATE_DIR/symfony_params.yml" "$SYMFONY_CONFIG_DIR/parameters.yml"

cd /vagrant
echo "Running composer install.."
su -c "cd /vagrant; composer install --no-interaction --prefer-dist" $VAGRANT_USER

echo "Starting mongod.."
service mongod start

echo "Populating database with data.." # TODO
echo "NYI"
# mongod korobi file.js


echo "Creating configuration for $WEBSERVER.."
if [[ $WEBSERVER == 'apache2' ]]; then
    FPREFIX="apache"
    WEBDIR="/etc/apache2"
    SITEDIR="$WEBDIR/sites-available"
    ENDIR="$WEBDIR/sites-enabled"
    SSL_DIR="$WEBDIR/ssl"

    a2enmod ssl
    a2enmod rewrite
else
    FPREFIX="nginx"
    WEBDIR="/etc/nginx"
    SITEDIR="$WEBDIR/sites-available"
    ENDIR="$WEBDIR/sites-enabled"
    SSL_DIR="$WEBDIR/ssl"
fi

# cp "$TEMPLATE_DIR/$FPREFIX.conf" "$WEBDIR/$FPREFIX.conf"
SSL_SAFE_DIR=$(echo $SSL_DIR | sed 's/\//\\\//g')
cat "$TEMPLATE_DIR/${FPREFIX}_site.conf" | sed -E "s/%ssl_dir%/$SSL_SAFE_DIR/" > "$SITEDIR/${FPREFIX}_site.conf"

echo "Generating files for local SSL.."
mkdir -p $SSL_DIR
cd $SSL_DIR
openssl req \
    -new \
    -newkey rsa:4096 \
    -days 3650 \
    -nodes \
    -x509 \
    -subj "/C=CA/ST=British Columbia/L=Vancouver/O=Korobi/OU=Web Development/CN=korobi.dev" \
    -keyout korobi.key \
    -out korobi.crt

echo "Enabling site for webserver.."
ln -s "$SITEDIR/${FPREFIX}_site.conf" "$ENDIR/${FPREFIX}_site.conf"

echo "Restarting $WEBSERVER.."
service $WEBSERVER restart

echo "Initializing Symfony.."
su -c 'cd /vagrant; composer dump' $VAGRANT_USER
curl -k https://korobi.dev &>/dev/null # Generates bootstrap.php.cache
su -c 'cd /vagrant; php app/console assetic:dump' $VAGRANT_USER

cd /vagrant/app
echo "Running tests in $(pwd).."
sudo -i -u $VAGRANT_USER phpunit .

TAIL_BASHRC=$(tail -1 /home/$VAGRANT_USER/.bashrc)
if [[ $TAIL_BASHRC != 'cd $SYMLINK_NAME' ]]; then
  echo "Appending bashrc to cd to directory.."
 sudo -i -u $VAGRANT_USER echo "cd $SYMLINK_NAME" >> /home/$VAGRANT_USER/.bashrc
fi

PROVISION_END=`date +%s`
echo "Provisioning ended: $(date)"
echo "Time taken: $(($PROVISION_END - $PROVISION_START)) seconds"
echo
echo "Done!"
echo "Connect to SSH via vagrant@localhost:${SSH_PORT} or visit the page on https://localhost:${HTTPS_PORT} or https://korobi.dev:${HTTPS_PORT}"

function cleanup() {  # TODO - actually use it somewhere
    echo "Cleanup check: Composer"
    if [[ -e /vagrant/vendor ]]; then
        rm -rv /vagrant/vendor
    fi

    echo -e "Done!\nCleanup check: npm"
    if [[ -e /vagrant/node_modules/ ]]; then
        rm -rv /vagrant/node_modules
    fi

    if [[ -e /vagrant/npm-debug.log ]]; then
        rm -v /vagrant/npm-debug.log
    fi

    echo -e "Done!\nCleanup check: binaries"
    if [[ -e /vagrant/bin ]]; then
        rm -rv /vagrant/bin
    fi

    echo -e "Done!\nCleanup check: Symfony parameters.yml"
    if [[ -e /vagrant/app/config/parameters.yml ]]; then
        rm -v /vagrant/app/config/parameters.yml
    fi

    echo -e "Done!\nCleanup check: Symfony cache"
    if [[ -e /vagrant/app/bootstrap.php.cache ]]; then
        rm -v /vagrant/app/bootstrap.php.cache
    fi
    echo "Done!"
}
