<?php
// Rutas a los archivos de flags compartidos mediante el volumen
$path_flag = '/opt/shared_flags/flag.txt';
$path_user = '/opt/shared_flags/user.txt';
$path_root = '/opt/shared_flags/root.txt';

// Función para leer la flag actual del archivo
function get_flag($path) {
    return file_exists($path) ? trim(file_get_contents($path)) : null;
}

$resultado = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['flag'])) {
    $input = trim($_POST['flag']);
    
    // Obtenemos las flags actuales (dinámicas)
    $current_flag = get_flag($path_flag);
    $current_user = get_flag($path_user);
    $current_root = get_flag($path_root);

    if ($input === $current_flag) {
        $resultado = "<div style='color: #00ff00;'>[Nivel Inicial] ¡Correcto! Has encontrado la flag en /home/sickos.</div>";
    } elseif ($input === $current_user) {
        $resultado = "<div style='color: #00ff00;'>[Usuario] ¡Excelente! Has obtenido la flag de user1.</div>";
    } elseif ($input === $current_root) {
        $resultado = "<div style='color: #ffcc00; font-weight: bold;'>[ROOT] ¡VICTORIA! Has comprometido totalmente la máquina.</div>";
    } else {
        $resultado = "<div style='color: #ff3333;'>Flag incorrecta o no encontrada.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validator - SickOs</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: #000000;
            color: #00ff41;
            font-family: 'Courier New', monospace;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        .container {
            position: relative;
            z-index: 1;
        }
        
        .box {
            border: 3px solid #00ff41;
            display: inline-block;
            padding: 50px 60px;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(15, 15, 40, 0.9) 100%);
            box-shadow: 
                0 0 30px rgba(0, 255, 65, 0.5),
                0 0 60px rgba(0, 255, 65, 0.2),
                inset 0 0 30px rgba(0, 255, 65, 0.1);
            border-radius: 5px;
            animation: boxGlow 3s ease-in-out infinite, boxFloat 4s ease-in-out infinite;
            position: relative;
        }
        
        @keyframes boxGlow {
            0%, 100% { 
                box-shadow: 
                    0 0 30px rgba(0, 255, 65, 0.5),
                    0 0 60px rgba(0, 255, 65, 0.2),
                    inset 0 0 30px rgba(0, 255, 65, 0.1);
            }
            50% { 
                box-shadow: 
                    0 0 50px rgba(0, 255, 65, 0.8),
                    0 0 80px rgba(0, 255, 65, 0.4),
                    inset 0 0 50px rgba(0, 255, 65, 0.2);
            }
        }
        
        @keyframes boxFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 0 0 20px rgba(0, 255, 65, 0.8);
            letter-spacing: 2px;
            animation: flicker 3s infinite, scanlines 0.15s infinite;
        }
        
        @keyframes flicker {
            0%, 100% { text-shadow: 0 0 20px rgba(0, 255, 65, 0.8); opacity: 1; }
            5% { text-shadow: 0 0 10px rgba(0, 255, 65, 0.4); opacity: 0.8; }
            10% { text-shadow: 0 0 20px rgba(0, 255, 65, 0.8); opacity: 1; }
            15% { text-shadow: 0 0 15px rgba(0, 255, 65, 0.6); opacity: 0.9; }
            20% { text-shadow: 0 0 20px rgba(0, 255, 65, 0.8); opacity: 1; }
        }
        
        @keyframes scanlines {
            0% { text-shadow: 0 0 20px rgba(0, 255, 65, 0.8), 2px 2px 0 rgba(0, 255, 65, 0.3); }
            100% { text-shadow: 0 0 20px rgba(0, 255, 65, 0.8), -2px -2px 0 rgba(0, 255, 65, 0.3); }
        }
        
        .subtitle {
            font-size: 0.9em;
            color: #00cc33;
            margin-bottom: 30px;
            opacity: 0.7;
            animation: pulse 2s ease-in-out infinite;
            letter-spacing: 1px;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin: 30px 0;
        }
        
        input {
            background: rgba(17, 17, 34, 0.9);
            border: 2px solid #00ff41;
            color: #00ff41;
            padding: 12px 15px;
            width: 100%;
            min-width: 350px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 1em;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.2);
            caret-color: #00ff41;
        }
        
        input:focus {
            outline: none;
            border-color: #00ff41;
            background: rgba(17, 17, 50, 0.95);
            box-shadow: 
                0 0 20px rgba(0, 255, 65, 0.5),
                inset 0 0 10px rgba(0, 255, 65, 0.1);
            transform: scale(1.02);
        }
        
        input::placeholder {
            color: rgba(0, 255, 65, 0.5);
        }
        
        button {
            background: linear-gradient(135deg, #00ff41 0%, #00cc33 100%);
            color: #000;
            border: none;
            padding: 12px 40px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            border-radius: 3px;
            transition: all 0.3s ease;
            letter-spacing: 1px;
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.3);
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }
        
        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
            transition: left 0.3s ease;
            z-index: -1;
        }
        
        button:hover {
            background: linear-gradient(135deg, #00cc33 0%, #009922 100%);
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.8);
            transform: translateY(-3px);
        }
        
        button:hover::before {
            left: 100%;
        }
        
        button:active {
            transform: translateY(-1px);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.6);
        }
        
        .resultado-container {
            margin-top: 30px;
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .resultado-container div {
            padding: 15px 25px;
            border-radius: 3px;
            border: 2px solid;
            animation: slideIn 0.5s ease-out;
            font-weight: bold;
            font-size: 1.1em;
            text-shadow: 0 0 10px currentColor;
            letter-spacing: 0.5px;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .resultado-container div[style*="color: #00ff00"] {
            border-color: #00ff00;
            background: rgba(0, 255, 0, 0.1);
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
        }
        
        .resultado-container div[style*="color: #ffcc00"] {
            border-color: #ffcc00;
            background: rgba(255, 204, 0, 0.1);
            box-shadow: 0 0 20px rgba(255, 204, 0, 0.5);
            animation: slideIn 0.5s ease-out, victory 0.6s ease-out infinite;
        }
        
        @keyframes victory {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .resultado-container div[style*="color: #ff3333"] {
            border-color: #ff3333;
            background: rgba(255, 51, 51, 0.1);
            box-shadow: 0 0 20px rgba(255, 51, 51, 0.5);
            animation: slideIn 0.5s ease-out, shake 0.3s ease-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .corner-decor {
            position: fixed;
            font-size: 2em;
            color: rgba(0, 255, 65, 0.1);
            pointer-events: none;
            z-index: 0;
        }
        
        .corner-top-left { top: 20px; left: 20px; animation: rotate 20s linear infinite; }
        .corner-top-right { top: 20px; right: 20px; animation: rotate-reverse 20s linear infinite; }
        .corner-bottom-left { bottom: 20px; left: 20px; animation: rotate-reverse 20s linear infinite; }
        .corner-bottom-right { bottom: 20px; right: 20px; animation: rotate 20s linear infinite; }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes rotate-reverse {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }
        
        #particleCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <canvas id="particleCanvas"></canvas>
    
    <div class="corner-decor corner-top-left">◆</div>
    <div class="corner-decor corner-top-right">◆</div>
    <div class="corner-decor corner-bottom-left">◆</div>
    <div class="corner-decor corner-bottom-right">◆</div>
    
    <div class="container">
        <div class="box">
            <h1>💀 SICKOS FLAG VALIDATOR 💀</h1>
            <div class="subtitle">// SISTEMA DE VERIFICACIÓN DE SEGURIDAD //</div>
            <form method="post">
                <input type="text" name="flag" placeholder="Introduce flag ssi{...}" required>
                <button type="submit">► VERIFICAR ◄</button>
            </form>
            <div class="resultado-container"><?php echo $resultado; ?></div>
        </div>
    </div>
    
    <script>
        // Canvas para efecto hacker de escritura
        const canvas = document.getElementById('particleCanvas');
        const ctx = canvas.getContext('2d');
        
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        
        const hackerTexts = [
            '$ analyzing target...',
            '> initializing connection',
            '>> establishing tunnel',
            '[*] system detected',
            '[+] access granted',
            '$ scanning vulnerabilities',
            '> deploying exploit',
            '[!] breach detected',
            '>> infiltrating network',
            '$ extracting data',
            '[+] payload executed',
            '> gaining privileges',
            '[*] root access obtained',
            '$ dumping credentials',
            '>> covering tracks',
            '[+] mission accomplished',
            '$ calculating next move',
            '> initializing backdoor',
            '[!] firewall bypassed',
            '>> encryption cracked'
        ];
        
        let textLines = [];
        let currentText = '';
        let currentIndex = 0;
        let charIndex = 0;
        let lineY = 50;
        
        function getRandomHackerText() {
            return hackerTexts[Math.floor(Math.random() * hackerTexts.length)];
        }
        
        function addNewLine() {
            if (lineY > canvas.height - 100) {
                textLines.shift();
                textLines.forEach((line, i) => {
                    line.y -= 25;
                });
            } else {
                lineY += 25;
            }
            
            currentText = getRandomHackerText();
            charIndex = 0;
        }
        
        function animateText() {
            // Limpiar canvas
            ctx.fillStyle = '#000000';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Dibujar líneas anteriores
            ctx.fillStyle = '#00ff41';
            ctx.font = '14px "Courier New"';
            ctx.textAlign = 'left';
            
            textLines.forEach(line => {
                ctx.fillText(line.text, 40, line.y);
            });
            
            // Escribir carácter actual
            if (currentText) {
                if (charIndex < currentText.length) {
                    currentText = currentText.substring(0, charIndex + 1);
                    ctx.fillText(currentText, 40, lineY);
                    charIndex++;
                    
                    // Efecto de cursor parpadeante
                    if (Math.floor(Date.now() / 500) % 2 === 0) {
                        ctx.fillText('▌', 40 + ctx.measureText(currentText).width, lineY);
                    }
                } else if (Math.random() < 0.02) {
                    // Guardar línea completada y crear nueva
                    textLines.push({ text: currentText, y: lineY });
                    addNewLine();
                }
            } else {
                addNewLine();
            }
            
            requestAnimationFrame(animateText);
        }
        
        // Redimensionar canvas cuando cambia la ventana
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
        
        addNewLine();
        animateText();
    </script>
</body>
</html>