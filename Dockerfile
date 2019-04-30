FROM debian
LABEL maintainer Chorder
#ADD sources.list /etc/apt/
RUN apt-get update
RUN apt-get install -y apt-utils mariadb-server mariadb-client php php-mysql php-gd php-curl php-xml zip net-tools ssh curl wget vim
RUN rm -rf /var/www/html/
ADD PHPMyWind_5.5 /var/www/html/
RUN chmod -R 777 /var/www/html/
ADD start.sh /
ADD mysql_init.sh /
RUN chmod +x /start.sh /mysql_init.sh
ENTRYPOINT /start.sh && bash
EXPOSE 80
WORKDIR /var/www/html/
