#!/bin/bash

#set iptables
systemctl start firewalld.service
systemctl enable firewalld.service
firewall-cmd --zone=public --add-port=80/tcp --permanent
firewall-cmd --zone=public --add-port=443/tcp --permanent
firewall-cmd --zone=public --add-port=8200/udp --permanent
firewall-cmd --reload

#SELinux set
sed -i 's/SELINUX=enforcing/SELINUX=disabled/g' /etc/selinux/config
setenforce 0

yum install -y vim >> /tmp/install_mixin.log
#安装ffmpeg
yum install -y epel-release
sudo rpm –import /etc/pki/rpm-gpg/RPM-GPG-KEY-EPEL-7
yum repolist
sudo rpm –import http://li.nux.ro/download/nux/RPM-GPG-KEY-nux.ro
sudo rpm -Uvh http://li.nux.ro/download/nux/dextop/el7/x86_64/nux-dextop-release-0-1.el7.nux.noarch.rpm
yum repolist
yum install -y ffmpeg >> /tmp/install_mixin.log

#安装加密相关组件
yum install -y libmcrypt libmcrypt-devel mcrypt mhash >> /tmp/install_mixin.log
#安装redis
yum install -y redis.x86_64 >> /tmp/install_mixin.log
#安装apache
# yum install -y httpd.x86_64 httpd-devel.x86_64 mod_ssl.x86_64 >> /tmp/install_mixin.log
#安装mysql
yum install -y mariadb mariadb-server  mariadb-devel >> /tmp/install_mixin.log
#安装PHP，及其扩展
yum install -y php php-devel php-common php-fpm php-ldap php-mbstring php-mysqlnd php-pdo php-process php-xml php-gd php-mcrypt php-pecl-redis php-pecl-igbinary php-pecl-geoip >> /tmp/install_mixin.log

#set php
mv /etc/php.ini /etc/php.ini_bk
cp /mixin/Conf/php.ini /etc
#set mysql
mv /etc/my.cnf /etc/my.cnf_bk
cp /mixin/Conf/my.cnf /etc
#set redis
mv /etc/redis.conf /etc/redis.conf_bk
cp /mixin/Conf/redis.conf /etc/

chown apache.apache /mixin
chown apache.apache /mixin/www -R
chown apache.apache /mixin/Cert -R

#systemctl restart httpd.service >> /tmp/install_mixin.log
#systemctl enable httpd.service >> /tmp/install_mixin.log
systemctl restart mariadb >> /tmp/install_mixin.log
systemctl enable mariadb >> /tmp/install_mixin.log
systemctl restart redis.service >> /tmp/install_mixin.log
systemctl enable redis.service >> /tmp/install_mixin.log

#set mysql password
mysqladmin -uroot password "sec08mysql"