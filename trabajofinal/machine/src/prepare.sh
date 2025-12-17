#!/bin/bash
set -e

echo "--- 1. Instalando Dependencias ---"
apt-get update && apt-get install -y \
    lighttpd \
    lighttpd-mod-webdav \
    php-cgi \
    cron \
    net-tools \
    curl \
    procps \
    binutils \
    openssh-server \
    sudo \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

mkdir -p /var/run/sshd
sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config

echo "--- 2. Configurando PHP ---"
lighty-enable-mod fastcgi
lighty-enable-mod fastcgi-php

echo "--- 3. Desplegando Web Original ---"
rm -rf /var/www/html/*
cp -r /opt/html/* /var/www/html/
mkdir -p /var/www/html/test

usermod -s /bin/bash www-data
chown -R www-data:www-data /var/www/html
chmod 777 /var/www/html/test

echo "--- 4. Configurando Lighttpd ---"
cp /opt/conf/lighttpd.conf /etc/lighttpd/lighttpd.conf
mkdir -p /var/run/lighttpd
chown -R www-data:www-data /var/run/lighttpd
mkdir -p /var/log/lighttpd
touch /var/log/lighttpd/error.log
chown -R www-data:www-data /var/log/lighttpd

chmod 1777 /tmp
chmod 1777 /var/tmp

mkdir -p /tmp/updates
chmod 755 /tmp/updates

echo "--- 5. Instalando Exploit Root ---"
cp /opt/bins/chkrootkit /usr/sbin/chkrootkit
chmod +x /usr/sbin/chkrootkit

cp /opt/conf/cron_exploit /etc/cron.d/chkrootkit
chmod 644 /etc/cron.d/chkrootkit
touch /etc/cron.d/chkrootkit

echo "--- 6. Configurando Usuarios y vulnerabilidades ---"
useradd -m -s /bin/bash sickos
useradd -m -s /bin/bash user1
echo "user1:rockyou" | chpasswd -c MD5
echo "sickos:sickos" | chpasswd -c MD5
echo "root:sickos" | chpasswd -c MD5

chown user1:user1 /tmp/updates

chgrp www-data /etc/shadow
chmod 640 /etc/shadow

echo "--- 7. Preparando Flags ---"
cp /opt/flag/flag.txt /home/sickos/flag.txt
chown sickos:sickos /home/sickos/flag.txt
chmod 755 /home/sickos

cp /opt/flag/user.txt /home/user1/user.txt
chown user1:user1 /home/user1/user.txt
chmod 600 /home/user1/user.txt

cp /opt/flag/root.txt /root/root.txt

echo "--- Instalación Completa ---"