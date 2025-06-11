<?php
require '../conexion.php';
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

$mensaje = "";
$registro_exitoso = false;

function generarToken($longitud = 6) {
    return strtoupper(substr(bin2hex(random_bytes($longitud)), 0, 6));
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


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $username = $_POST['username'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $codigo_ingresado = $_POST['codigo'] ?? '';

    // ✅ ENVÍO DEL TOKEN
    if (isset($_POST['enviar_token'])) {
        if ($correo) {
            $_SESSION['correo_temp'] = $correo;
            $token = generarToken();
            $_SESSION['registro_token'] = $token;

            $mensaje_correo = "Tu código de verificación para registrarte es: $token";

            if (enviarCorreo($correo, "Código de verificación", $mensaje_correo)) {
                $mensaje = "✅ Código enviado al correo.";
            } else {
                $mensaje = "❌ Error al enviar el código.";
            }
        } else {
            $mensaje = "⚠️ Ingresa el correo para recibir tu código.";
        }
    }

    // ✅ REGISTRO DEL USUARIO
 if (isset($_POST['registrar'])) {
    if ($nombre && $apellido && $username && $telefono && $correo && $clave && $codigo_ingresado) {
        if (
            trim($codigo_ingresado) === ($_SESSION['registro_token'] ?? '') &&
            trim($correo) === ($_SESSION['correo_temp'] ?? '')
        ) {
            // Verificar si ya existe correo o username
            $stmt = $conexion->prepare("SELECT id FROM clientes WHERE correo = ? OR username = ?");
            $stmt->bind_param("ss", $correo, $username);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                $mensaje = "❌ El correo o el nombre de usuario ya están registrados.";
            } else {
                // Insertar si no existe
                $clave_hash = password_hash($clave, PASSWORD_DEFAULT);
                $stmt = $conexion->prepare("INSERT INTO clientes (nombre, apellido, telefono, correo, username, contraseña_hash) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $nombre, $apellido, $telefono, $correo, $username, $clave_hash);

                if ($stmt->execute()) {
                    // Limpiar sesión y redirigir al login
                    unset($_SESSION['registro_token']);
                    unset($_SESSION['correo_temp']);
                    $_SESSION['registro_exitoso'] = true;
                    header("Location: login.php");
                    exit;
                } else {
                    $mensaje = "❌ Error al registrar: " . $stmt->error;
                }
            }
        } else {
            $mensaje = "❌ Código inválido o correo no coincide.";
        }
    } else {
        $mensaje = "⚠️ Completa todos los campos.";
    }
}


}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../css/registro.css">
    <link rel="shortcut icon" href="/imagenes/logo.jpg" type="image/x-icon">
</head>
<body>

<?php if ($registro_exitoso): ?>
<script>
    window.onload = function() {
        alert("✅ ¡Registro exitoso!\\nYa puedes iniciar sesión.");
        window.location.href = "/mvc/login.php";
    }
</script>
<?php endif; ?>

<div class="registro-container">
    <h2>Registro de Usuario</h2>

    <?php if (!empty($mensaje)): ?>
        <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>

        <label>Apellido:</label>
        <input type="text" name="apellido" value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>" required>

        <label>Nombre de usuario:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>" required>

        <label>Contraseña:</label>
        <input type="password" name="clave" required>

        <label>Correo:</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($_POST['correo'] ?? $_SESSION['correo_temp'] ?? '') ?>" required>

        <button type="button" onclick="enviarCodigo()" class="btn-registrar">Enviar código al correo</button>


        <label>Código recibido:</label>
        <input type="text" name="codigo" value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>" required>

        <button type="submit" name="registrar" class="btn-registrar">Registrarse</button>
        <a href="/mvc/login.php" class="btn-volver">← Volver al inicio de sesión</a>

    </form>
</div>
<script>
function enviarCodigo() {
    const correo = document.querySelector('input[name="correo"]');
    const form = document.querySelector('form');

    if (!correo.value.trim()) {
        alert("⚠️ Por favor ingresa un correo válido.");
        correo.focus();
        return;
    }

    // Crear un input oculto con name="enviar_token"
    const tokenInput = document.createElement("input");
    tokenInput.type = "hidden";
    tokenInput.name = "enviar_token";
    tokenInput.value = "1";
    form.appendChild(tokenInput);

    form.submit();
}
</script>

</body>
</html>
