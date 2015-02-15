#!/bin/bash

echo "Pulling changes..."
git pull

echo "Assigning directory permissions..."
chmod -R 777 app/cache/dev

echo "Dumping asset files..."
php app/console assetic:dump

echo "Clearing cache..."
php app/console cache:clear
