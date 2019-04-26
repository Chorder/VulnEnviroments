FROM debian
MAINTAINER Chorder
#ADD sources.list /etc/apt/
RUN apt-get update
RUN apt-get install -y apt-utils mariadb-server mariadb-client php php-mysql php-gd php-xml php-curl zip net-tools ssh curl wget vim
ADD start.sh /
RUN chmod +x /start.sh
RUN rm -rf /var/www/html/
ADD 5.1 /var/www/html/
RUN chmod -R 777 /var/www/html/
WORKDIR /var/www/html/
ENTRYPOINT /start.sh && bash
EXPOSE 80
