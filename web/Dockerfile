FROM debian:latest

# 1. Instalamos dependencias
# AÑADIDO: 'net-tools' y 'binutils' para que chkrootkit funcione sin errores
RUN apt-get update && apt-get install -y \
    lighttpd \
    lighttpd-mod-webdav \
    php-cgi \
    cron \
    procps \
    net-tools \
    binutils \
    && rm -rf /var/lib/apt/lists/*

# 2. Activamos PHP (FastCGI)
RUN lighttpd-enable-mod fastcgi && lighttpd-enable-mod fastcgi-php

# 3. Copiamos los archivos
COPY ./www/ /var/www/
COPY ./lighttpd.conf /etc/lighttpd/lighttpd.conf

# Copiamos el exploit y el cron
COPY ./chkrootkit /usr/sbin/chkrootkit
COPY ./cron_chkrootkit /etc/cron.daily/chkrootkit

# 4. Permisos
# Creamos la carpeta 'test' y asignamos permisos
RUN mkdir -p /var/www/test \
    && chown -R www-data:www-data /var/www \
    && chmod 777 /var/www \
    && chmod 777 /var/www/test

# Permisos de ejecución para el exploit/cron
RUN chmod +x /usr/sbin/chkrootkit \
    && chmod +x /etc/cron.daily/chkrootkit

# 5. [EXTRA] Creamos la flag falsa para simular el final oficial
RUN echo "ssi{4876c5c9a6420387d66104e5bc32f5d3}" > /root/root.txt

# 6. Arrancar
CMD service cron start && lighttpd -D -f /etc/lighttpd/lighttpd.conf
