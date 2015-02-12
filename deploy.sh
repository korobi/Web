#!/bin/bash
git pull
php app/console assetic:dump
cd app/
phpunit
