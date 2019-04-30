FROM debian
MAINTAINER Chorder

#ADD sources.list /etc/apt/
RUN apt-get update
RUN apt-get install -y mariadb-server mariadb-client php php-mysql php-gd zip net-tools vim

RUN rm -rf /var/www/html
ADD alpha /var/www/html/
ADD start.sh /
ADD mysql_init.sh /

RUN chmod 777 -R /var/www/html
RUN chmod +x /start.sh /mysql_init.sh
WORKDIR /var/www/html/
ENTRYPOINT /start.sh && bash
EXPOSE 80
