#!/bin/bash

service apache2 start
service mysql start
if [ -f /mysql_init.sh ];then
  /mysql_init.sh
  rm -rf /mysql_init.sh
fi
