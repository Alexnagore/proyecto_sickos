FROM debian:latest

# Evitar preguntas interactivas
ENV DEBIAN_FRONTEND=noninteractive

# 1. Instalar herramientas
# HE ELIMINADO 'wordlists' DE ESTA LISTA PARA QUE NO FALLE
RUN apt-get update && apt-get install -y \
    openssh-server \
    curl \
    wget \
    git \
    nano \
    net-tools \
    iproute2 \
    iputils-ping \
    nmap \
    netcat-traditional \
    gobuster \
    john \
    procps \
    sudo \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Descargar e instalar Rockyou manualmente
# Como Debian no tiene el paquete, lo bajamos de un repositorio de GitHub
RUN mkdir -p /usr/share/wordlists
RUN wget https://github.com/praetorian-inc/Hob0Rules/raw/master/wordlists/rockyou.txt.gz -O /usr/share/wordlists/rockyou.txt.gz
RUN gzip -d /usr/share/wordlists/rockyou.txt.gz

# 3. Configurar SSH y Directorios
RUN mkdir -p /var/run/sshd

# --- CREACIÓN DEL USUARIO DUMMY ---
RUN useradd -m -s /bin/bash dummy
RUN echo 'dummy:palangana' | chpasswd
RUN usermod -aG sudo dummy

# Configuraciones SSH
RUN sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config
RUN sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config

# Generar claves de host (Vital para que no se cierre la conexión)
RUN ssh-keygen -A

# 4. Entorno
WORKDIR /home/dummy

# Exponemos el puerto 22
EXPOSE 22

CMD ["/usr/sbin/sshd", "-D"]