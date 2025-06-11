<?php
require '../conexion.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

$mensaje = "";

// Función para generar token
function generarToken($longitud = 6) {
    return strtoupper(substr(bin2hex(random_bytes($longitud)), 0, 6)); // Ej: F3A29C
}

function guardarToken($conexion, $user_id, $token, $tipo) {
    $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $stmt = $conexion->prepare("INSERT INTO tokens (user_id, token, type, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $token, $tipo, $expira);
    $stmt->execute();
}

function enviarCorreo($destinatario, $asunto, $mensaje) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USER');
        $mail->Password   = getenv('SMTP_PASS');
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $from = getenv('SMTP_USER');
        if (!$from) {
            throw new Exception("SMTP_USER no definido en entorno");
        }

        $mail->setFrom($from, 'ServiciosJK');
        $mail->addAddress($destinatario);
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje;
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "❌ Error al enviar: " . $mail->ErrorInfo;
        return false;
    }
}



// Lógica para envío del código
if (isset($_POST['correo_submit'])) {
    $correo = $_POST['correo'] ?? '';

    if ($correo) {
        $stmt = $conexion->prepare("SELECT id, nombre FROM clientes WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            $user_id = $usuario['id'];
            $nombre = $usuario['nombre'];

            $token = generarToken();
            guardarToken($conexion, $user_id, $token, 'reset');

            $mensaje_correo = "Hola $nombre,\n\nTu código para restablecer contraseña es: $token\n\nExpira en 1 hora.";
            if (enviarCorreo($correo, "Código de recuperación", $mensaje_correo)) {
                $mensaje = "✅ Código enviado a tu correo.";
            } else {
                $mensaje = "❌ Error al enviar el correo.";
            }
        } else {
            $mensaje = "❌ Correo no encontrado.";
        }
    }
}

// Lógica para verificar código
if (isset($_POST['token_submit'])) {
    $codigo = $_POST['codigo'] ?? '';

    if ($codigo) {
        $stmt = $conexion->prepare("SELECT token FROM tokens WHERE token = ? AND type = 'reset' AND is_used = 0 AND expires_at > NOW()");
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            header("Location: nueva_contraseña.php?token=$codigo");
            exit;
        } else {
            $mensaje = "❌ Código inválido o expirado.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
      <link rel="shortcut icon" href="/imagenes/logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../css/registro.css">
    <style>
        .form-box {
            background-color: #1c1c1c;
            color: #eee;
            padding: 20px;
            border-radius: 12px;
            max-width: 400px;
            margin: 40px auto;
            box-shadow: 0 0 12px rgba(0,0,0,0.4);
        }
        .form-box h2 {
            color:white;
        }
        .form-box input {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: none;
        }
        .form-box button {
            padding: 10px;
            background: #00cc66;
            color: #fff;
            border: none;
            width: 100%;
            border-radius: 6px;
        }
        .mensaje {
            color: yellow;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="form-box">
    <h2>Recuperar Contraseña</h2>

    <?php if (!empty($mensaje)): ?>
        <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Correo electrónico:</label>
        <input type="email" name="correo" required>
        <button type="submit" name="correo_submit">Enviar código</button>
    </form>

    <hr>

    <form method="POST">
        <label>Código recibido:</label>
        <input type="text" name="codigo" required>
        <button type="submit" name="token_submit">Verificar código</button>
    </form>

    <div style="text-align:center;margin-top:10px;">
        <a href="/mvc/login.php" style="color:#00ccff;">Volver al inicio de sesión</a>
    </div>
</div>

</body>
</html>
