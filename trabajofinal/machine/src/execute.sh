#!/bin/bash

echo "--- Iniciando servicios de SickOs (Portado) ---"

service cron start

service lighttpd start

tail -f /var/log/lighttpd/error.log
