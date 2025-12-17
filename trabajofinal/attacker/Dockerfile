FROM debian:latest

# Evitar preguntas interactivas
ENV DEBIAN_FRONTEND=noninteractive

# 1. Instalar herramientas
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
    bash-completion \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN ln -sf /usr/sbin/john /usr/bin/john

# 2. Wordlists (rockyou + common)
RUN mkdir -p /usr/share/wordlists && \
    wget https://github.com/praetorian-inc/Hob0Rules/raw/master/wordlists/rockyou.txt.gz \
      -O rockyou.txt.gz && \
    wget https://raw.githubusercontent.com/v0re/dirb/master/wordlists/common.txt \
      -O common.txt && \
    gzip -d /usr/share/wordlists/rockyou.txt.gz

# 3. Configurar SSH
RUN mkdir -p /var/run/sshd && \
    sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config && \
    sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config && \
    ssh-keygen -A

# 4. Crear usuario dummy
RUN useradd -m -s /bin/bash dummy && \
    echo 'dummy:palangana' | chpasswd && \
    usermod -aG sudo dummy

# 5. Mejorar entorno Bash del usuario dummy
RUN echo "if [ -f /etc/bash_completion ]; then . /etc/bash_completion; fi" >> /home/dummy/.bashrc && \
    echo "export PS1='\\[\\033[01;32m\\]\\u@\\h\\[\\033[00m\\]:\\[\\033[01;34m\\]\\w\\[\\033[00m\\]\\$ '" >> /home/dummy/.bashrc && \
    echo "alias ls='ls --color=auto'" >> /home/dummy/.bashrc && \
    chown dummy:dummy /home/dummy/.bashrc

# 6. Entorno y arranque
WORKDIR /home/dummy
EXPOSE 22
CMD ["/usr/sbin/sshd", "-D"]
