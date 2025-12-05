# Imagen base mínima
FROM debian

COPY html /opt/html/
COPY bins /opt/bins/
COPY conf /opt/conf/
COPY src /opt/src/

# Damos permisos de ejecución a los scripts
RUN chmod +x /opt/src/prepare.sh /opt/src/execute.sh

# Ejecutamos la preparación
WORKDIR /opt/src
RUN ./prepare.sh

# Exponemos el puerto y arrancamos
EXPOSE 80
CMD ["/opt/src/execute.sh"]
