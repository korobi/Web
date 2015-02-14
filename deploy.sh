#!/bin/bash
echo "Pulling..."
git pull
echo "chmoding..."
chmod -R 777 app/cache/dev
echo "Making asset files..."
php app/console assetic:dump
echo "Clearing cache..."
php app/console cache:clear
echo "Running tests..."
cd app/
phpunit
