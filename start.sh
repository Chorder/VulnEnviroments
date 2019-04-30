#!/bin/bash
#!/bin/bash
service mysql start
service apache2 start
if [ -f /mysql_init.sh ];then
	/mysql_init.sh
	rm -rf /mysql_init.sh
fi 

