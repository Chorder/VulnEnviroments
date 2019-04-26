FROM debian
MAINTAINER chorder
WORKDIR /root/
#ADD sources.list /etc/apt/
RUN apt-get update
RUN apt-get install -y mariadb-server mariadb-client php php-mysql php-gd zip net-tools
ADD DVWA-master.zip /root/
RUN unzip /root/DVWA-master.zip -d /var/www/
RUN rm -rf /etc/apache2/sites-enabled/000-default.conf
ADD dvwa.conf /etc/apache2/sites-enabled/
RUN cp /var/www/DVWA-master/config/config.inc.php.dist /var/www/DVWA-master/config/config.inc.php
RUN sed -i 's/'root'/dvwa/g' /var/www/DVWA-master/config/config.inc.php
RUN sed -i 's/^allow_url_include.*$/allow_url_include = On/g' /etc/php/7.0/apache2/php.ini
ADD start.sh /
ADD mysql_init.sh /
RUN chmod 777 -R /var/www/DVWA-master
RUN chmod +x /start.sh
RUN chmod +x /mysql_init.sh
ENTRYPOINT /start.sh 
EXPOSE 80
