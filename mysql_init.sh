mysql -e "create database dvwa;"
mysql -e "grant all privileges on dvwa.* to dvwa identified by 'p@ssw0rd';"
mysql -e "flush privileges"

