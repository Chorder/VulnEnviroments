FROM debian
MAINTAINER Chorder
WORKDIR /root/
ADD sources.list /etc/apt/
ADD DVWA-master.zip /root/
ADD start.sh /root/
RUN apt-get update
RUN apt-get install -y mariadb-server mariadb-client php php-mysql php-gd zip net-tools
RUN unzip /root/DVWA-master.zip -d /var/www/
RUN rm -rf /etc/apache2/sites-enabled/000-default.conf
ADD dvwa.conf /etc/apache2/sites-enabled/
RUN cp /var/www/DVWA-master/config/config.inc.php.dist /var/www/DVWA-master/config/config.inc.php
RUN sed -i 's/'root'/dvwa/g' /var/www/DVWA-master/config/config.inc.php
RUN sed -i 's/^allow_url_include.*$/allow_url_include = On/g' /etc/php/7.0/apache2/php.ini
RUN chmod 777 -R /var/www/DVWA-master
RUN chmod +x /root/start.sh
ENTRYPOINT /root/start.sh && bash
EXPOSE 80
