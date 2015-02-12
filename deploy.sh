#!/bin/bash
git pull
chmod -R 777 app/cache/dev
php app/console assetic:dump
cd app/
phpunit
