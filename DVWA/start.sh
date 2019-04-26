#!/bin/bash

service apache2 start
service mysql start
mysql -e "grant all privileges on dvwa.* to dvwa identified by 'p@ssw0rd';"
mysql -e "flush privileges"
mysql -e "create database dvwa;"
