#!/usr/bin/env bash
#This is config for vagrant
#@todo refactor later
#
echo "export MYSQL_PASSWORD=phanbook" >>/etc/profile

echo "export DB_NAME=phanbook" >> /etc/profile

echo "export ROOT_DIR=/usr/share/nginx/html/www/" >> /etc/profile

echo "export ENV=development" >> /etc/profile

source /etc/profile
