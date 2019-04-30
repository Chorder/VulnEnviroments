mysql -e "grant all privileges on test.* to test identified by 'test';"
mysql -e "flush privileges"
mysql -e "create database test;"
