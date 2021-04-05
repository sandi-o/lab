#!/bin/bash
cd /var/www/html/lab
php artisan migrate --force
php artisan config:cache
php artisan l5-swagger:generate