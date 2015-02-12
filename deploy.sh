#!/bin/bash
git pull
sudo chmod -R 777 app/cache/dev
php app/console assetic:dump
cd app/
phpunit
