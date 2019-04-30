FROM debian
MAINTAINER Chorder
#ADD sources.list /etc/apt/
RUN apt-get update
RUN apt-get install -y apt-utils mariadb-server mariadb-client php php-mysql php-gd zip net-tools ssh curl wget vim
WORKDIR /var/www/html/
ADD start.sh /
ADD mysql_init.sh /
RUN chmod +x /start.sh /mysql_init.sh
EXPOSE 80
ENTRYPOINT /start.sh && bash
