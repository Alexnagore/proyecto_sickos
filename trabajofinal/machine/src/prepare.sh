#!/bin/bash
set -e

echo "--- 1. Instalando Dependencias ---"
# CAMBIO CLAVE: Añadimos 'lighttpd-mod-webdav'
apt-get update && apt-get install -y \
    lighttpd \
    lighttpd-mod-webdav \
    php-cgi \
    cron \
    net-tools \
    curl \
    vim \
    procps \
    sudo \
    && apt-get clean

echo "--- 2. Configurando PHP ---"
lighty-enable-mod fastcgi
lighty-enable-mod fastcgi-php

echo "--- 3. Desplegando Web Original ---"
rm -rf /var/www/html/*
cp -r /opt/html/* /var/www/html/
chown -R www-data:www-data /var/www/html
chmod 777 /var/www/html/test

echo "--- 4. Configurando Lighttpd ---"
cp /opt/conf/lighttpd.conf /etc/lighttpd/lighttpd.conf

# Carpetas de sistema y logs
mkdir -p /var/run/lighttpd
chown -R www-data:www-data /var/run/lighttpd
chmod 777 /var/tmp

# FIX: Creamos el log vacío para que 'tail' no falle al arrancar
touch /var/log/lighttpd/error.log
chown www-data:www-data /var/log/lighttpd/error.log

echo "--- 5. Instalando Exploit Root ---"
cp /opt/bins/chkrootkit /usr/sbin/chkrootkit
chmod +x /usr/sbin/chkrootkit

cp /opt/conf/cron_exploit /etc/cron.d/chkrootkit
chmod 644 /etc/cron.d/chkrootkit
touch /etc/cron.d/chkrootkit

echo "--- 6. Configurando Usuarios ---"
useradd -m -s /bin/bash sickos
echo "sickos:sickos" | chpasswd
echo "root:sickos" | chpasswd

echo "--- Instalación Completa ---"
