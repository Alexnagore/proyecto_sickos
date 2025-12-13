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
Durante esta fase se analiza la superficie de ataque del servidor web.

Se identifica que el directorio `/test/` es accesible y potencialmente vulnerable.  
El objetivo es comprobar si el servidor permite el método HTTP **PUT**, lo cual supondría una mala configuración de seguridad.

Si el método **PUT** está habilitado, es posible subir archivos directamente al servidor, abriendo la puerta a la explotación.

---

## 🔓 FASE 2: Acceso Inicial (Reverse Shell)
Confirmada la vulnerabilidad, se procede a obtener acceso remoto al sistema.

1. Se identifica la dirección IP de la máquina atacante, que actuará como destino de la conexión inversa.
2. Se prepara un payload que fuerza al servidor a iniciar una **reverse shell**.
3. Se crea un archivo PHP que permite ejecutar acciones en el servidor a través de peticiones web.
4. Ambos archivos se suben al directorio vulnerable `/test/` aprovechando el método PUT.
5. La máquina atacante se pone a la escucha en un puerto determinado.
6. Se ejecuta el archivo PHP, provocando que la víctima se conecte de vuelta.

Como resultado, se obtiene una shell con el usuario **www-data**, con privilegios limitados.

---

## 🧗 FASE 3: Escalada de Privilegios (Becoming Root)
Con acceso inicial al sistema, se inicia la fase de escalada de privilegios.

Se detecta que el sistema utiliza una versión vulnerable de **chkrootkit (0.49)** y que este se ejecuta automáticamente mediante tareas programadas.

Esta versión presenta una vulnerabilidad que permite ejecutar un archivo llamado `update` ubicado en `/tmp` con privilegios de administrador.

Aprovechando este comportamiento, se crea un archivo malicioso que modifica los permisos del binario `/bin/bash`, activando el bit **SUID**.

Cuando la tarea programada se ejecuta, el binario queda preparado para permitir la elevación de privilegios.

---

## 🏆 FASE 4: Looting (Victoria)
Tras la ejecución de la tarea programada:

- Se comprueba que `/bin/bash` tiene el bit **SUID** activo.
- Se lanza una shell con privilegios elevados.
- Se obtiene acceso completo como **root**.

Finalmente, se accede a las flags del sistema:

- Flag de usuario: `/home/sickos/user.txt`
- Flag de root: `/root/root.txt`

🏴‍☠️ **¡Máquina completamente comprometida!**

---

## ✅ Conclusión
- Se explotó una mala configuración del servidor web al permitir el método **PUT**.
- Se obtuvo acceso inicial mediante una **reverse shell**.
- Se escaló privilegios explotando una vulnerabilidad conocida en **chkrootkit 0.49**.
- Se consiguió control total del sistema como **root**.
