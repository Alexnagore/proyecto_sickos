#!/bin/bash

echo "--- Iniciando servicios de SickOs (Portado) ---"

# Seguridad anti-crash: Aseguramos el log
mkdir -p /var/log/lighttpd
touch /var/log/lighttpd/error.log
chown -R www-data:www-data /var/log/lighttpd

service cron start
service lighttpd start

echo "--- Servicios iniciados. Logs: ---"
tail -f /var/log/lighttpd/error.log