#!/usr/bin/env bash

## Configuration ##
WEBSERVER="apache2" # apache2 or nginx

TEMPLATE_DIR="/vagrant/templates" # dir where all template files are.
SSL_DIR="/home/vagrant/ssl" # dir to put the generated SSL certificate

SYMFONY_APP_DIR="/vagrant/app"
SYMFONY_CONFIG_DIR="$SYMFONY_APP_DIR/config"
## End Of Configuration ##

PROVISION_START=`date +%s`
echo "Starting to provision: $(date)"

cd /vagrant

echo "Appending to hosts file"
echo '127.0.0.1 korobi.dev' >> /etc/hosts

echo "Adding ruby PPA.."
apt-add-repository ppa:brightbox/ruby-ng -y

echo "Adding key for MongoDB.."
apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
echo "deb http://repo.mongodb.org/apt/ubuntu "$(lsb_release -sc)"/mongodb-org/3.0 multiverse" | tee /etc/apt/sources.list.d/mongodb-org-3.0.list

echo "Updating packages.."
apt-get update -y && apt-get upgrade -y
apt-get install mongodb-org php5 openssl ruby2.1 nodejs $WEBSERVER -y

echo "Installing composer.."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/bin/composer

echo "Installing bundler & ruby project dependencies.."
gem install bundler
sudo -u vagrant bundle install

echo "Installing nodejs project dependencies.."
sudo -u vagrant npm install

echo "Symlinking /vagrant to /home/vagrant"
ln -s /vagrant /home/vagrant/korobi-web

echo "Copying params.yml.."
cp "$TEMPLATE_DIR/symfony_params.yml" "$SYMFONY_CONFIG_DIR/parameters.yml"

cd /vagrant
echo "Running composer install.."
sudo -u vagrant composer install

echo "Starting mongod.."
service mongod start

echo "Creating databases.." # TODO
echo "NYI"
# mongoimport --db korobi --collection channels --file "$TEMPLATE_DIR/channels.json" --jsonArray
# mongoimport --db korobi --collection chat_indexes --file "$TEMPLATE_DIR/chat_indexes.json" --jsonArray
# mongoimport --db korobi --collection chats  --file "$TEMPLATE_DIR/chats.json" --jsonArray
# mongoimport --db korobi --collection networks --file "$TEMPLATE_DIR/networks.json" --jsonArray

echo "Populating databases with dummy data.." # TODO
echo "NYI"

echo "Generating files for local SSL.."
mkdir -p $SSL_DIR
cd $SSL_DIR
openssl req \
    -new \
    -newkey rsa:4096 \
    -days 365 \
    -nodes \
    -x509 \
    -subj "/C=US/ST=Localhost/L=Localhost/O=Korobi/CN=korobi.dev" \
    -keyout korobi.key \
    -out korobi.crt

echo "Creating configuration for $WEBSERVER.."
if [[ $WEBSERVER == 'apache2' ]]; then
  FPREFIX="apache"
  WEBDIR="/etc/apache2"
  SITEDIR="$WEBDIR/sites-available"
  ENDIR="$WEBDIR/sites-enabled"
else
  FPREFIX="nginx"
  WEBDIR="/etc/nginx"
  SITEDIR="$WEBDIR/sites-available"
  ENDIR="$WEBDIR/sites-enabled"
fi

cp "$TEMPLATE_DIR/$FPREFIX.conf" "$WEBDIR/$FPREFIX.conf"
cat "$TEMPLATE_DIR/${FPREFIX}_site.conf" | sed -E "s/%ssl_dir%/$SSL_DIR/" > "$SITEDIR/${FPREFIX}_site.conf"

echo "Enabling site for webserver.."
ln -s "$SITEDIR/$FPREFIX_site.conf" "$ENDIR/${FPREFIX}_site.conf"

echo "Restarting $WEBSERVER.."
service $WEBSERVER restart

cd /vagrant
echo "Running tests.."
sudo -u vagrant phpunit

PROVISION_END=`date +%s`
echo "Provisioning ended: $(date)"
echo "Time taken: $(($PROVISION_END - $PROVISION_START)) seconds"
echo
echo "Done!"
echo "Connect to SSH via vagrant@localhost:2222 or visit the page on https://localhost:4443 or https://korobi.dev:4443"
