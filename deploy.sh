#!/bin/bash
if [ "$(hostname)" == "iris.solas.io" ]; then
    ssh-agent bash -c 'ssh-add /home/kashike/.ssh/deploy_key'
fi
git pull
sudo chmod -R 777 app/cache/dev
php app/console assetic:dump
cd app/
phpunit
