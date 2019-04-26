FROM debian
MAINTAINER YourName

# 如果需要自定义源，一开始就添加
#ADD sources.list /etc/apt/

# 一开始最好先更新，这样后面在此基础上构建起来会快
RUN apt-get update
RUN apt-get install -y apt-utils mariadb-server mariadb-client php php-mysql php-gd php-curl php-xml zip net-tools ssh curl wget vim

# 做一些后续改动不会很大的工作
RUN rm -rf /var/www/html/
ADD oscommerce-2.3.4.1/catalog /var/www/html/
RUN chmod -R 777 /var/www/html/
ADD start.sh /
ADD mysql_init.sh /
RUN chmod +x /start.sh
RUN chmod +x /mysql_init.sh

# 一些后续可能会有改动和微调工作放在最后

WORKDIR /var/www/html/
ENTRYPOINT /start.sh && bash
EXPOSE 80
