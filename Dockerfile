FROM debian
MAINTAINER Chorder
#ADD sources.list /etc/apt/
RUN apt-get update
RUN apt-get install -y apt-utils mariadb-server mariadb-client php php-mysql php-gd php-curl php-xml zip net-tools ssh curl wget vim
RUN rm -rf /var/www/html/
ADD oscommerce-2.3.4.1/catalog /var/www/html/
RUN chmod -R 777 /var/www/html/
ADD start.sh /
ADD mysql_init.sh /
RUN chmod +x /start.sh
RUN chmod +x /mysql_init.sh
WORKDIR /var/www/html/
ENTRYPOINT /start.sh && bash
EXPOSE 80
