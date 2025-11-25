FROM debian:latest

# 1. Instalamos dependencias
RUN apt-get update && apt-get install -y \
    lighttpd \
    php-cgi \
    cron \
    procps \
    && rm -rf /var/lib/apt/lists/*

# 2. Activamos PHP (FastCGI)
# Esto crea enlaces simbólicos que la línea "include_shell" leerá
RUN lighttpd-enable-mod fastcgi && lighttpd-enable-mod fastcgi-php

# 3. Copiamos los archivos
# IMPORTANTE: Copiamos el CONTENIDO de tu carpeta 'www' directamente a la raíz '/var/www'
COPY ./www/ /var/www/

# Copiamos tu configuración corregida
COPY ./lighttpd.conf /etc/lighttpd/lighttpd.conf

# Copiamos el exploit y el cron
COPY ./chkrootkit /usr/sbin/chkrootkit
COPY ./cron_chkrootkit /etc/cron.daily/chkrootkit

# 4. Permisos
# Damos permisos al usuario www-data sobre la carpeta raíz
RUN chown -R www-data:www-data /var/www \
    && chmod 777 /var/www \
    && chmod 777 /var/www/test

# Permisos de ejecución
RUN chmod +x /usr/sbin/chkrootkit \
    && chmod +x /etc/cron.daily/chkrootkit

# 5. Arrancar
CMD service cron start && lighttpd -D -f /etc/lighttpd/lighttpd.conf