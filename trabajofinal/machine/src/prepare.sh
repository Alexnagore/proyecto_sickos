#!/bin/bash
set -e

echo "--- 1. Instalando Dependencias ---"
# FIX: Añadimos 'binutils' (CRÍTICO para que chkrootkit funcione)
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
    binutils \
    && apt-get clean

echo "--- 2. Configurando PHP ---"
lighty-enable-mod fastcgi
lighty-enable-mod fastcgi-php

echo "--- 3. Desplegando Web Original ---"
rm -rf /var/www/html/*
cp -r /opt/html/* /var/www/html/

# FIX: Creamos la carpeta explícitamente para evitar error "No such file"
mkdir -p /var/www/html/test

chown -R www-data:www-data /var/www/html
chmod 777 /var/www/html/test

echo "--- 4. Configurando Lighttpd ---"
cp /opt/conf/lighttpd.conf /etc/lighttpd/lighttpd.conf

# Carpetas de sistema y logs
mkdir -p /var/run/lighttpd
chown -R www-data:www-data /var/run/lighttpd
chmod 777 /var/tmp

# Creamos el log vacío
mkdir -p /var/log/lighttpd
touch /var/log/lighttpd/error.log
chown -R www-data:www-data /var/log/lighttpd

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