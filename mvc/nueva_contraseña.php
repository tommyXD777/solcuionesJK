<?php
require '../conexion.php';
session_start();

$mensaje = "";
$token = $_GET['token'] ?? '';

if (!$token) {
    exit("Token no válido.");
}

// Verificar si el token es válido
$stmt = $conexion->prepare("SELECT t.user_id, c.correo FROM tokens t 
    JOIN clientes c ON t.user_id = c.id 
    WHERE t.token = ? AND t.type = 'reset' AND t.expires_at > NOW() AND t.is_used = 0");
$stmt->bind_param("s", $token);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    exit("❌ Token inválido, expirado o ya usado.");
}

$datos = $resultado->fetch_assoc();
$user_id = $datos['user_id'];
$correo = $datos['correo'];

// Si se envía el formulario para nueva contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva = $_POST['nueva'] ?? '';
    $repite = $_POST['repite'] ?? '';

    if ($nueva && $repite) {
        if ($nueva === $repite) {
            $hash = password_hash($nueva, PASSWORD_DEFAULT);

            // ✅ Actualizar contraseña
            $stmt = $conexion->prepare("UPDATE clientes SET contraseña_hash = ? WHERE id = ?");
            $stmt->bind_param("si", $hash, $user_id);
            $stmt->execute();

            // ✅ Marcar el token como usado (forma correcta)
            $stmt_token = $conexion->prepare("UPDATE tokens SET is_used = 1 WHERE token = ?");
            $stmt_token->bind_param("s", $token);
            $stmt_token->execute();

            $mensaje = "✅ Contraseña restablecida con éxito. Ya puedes iniciar sesión.";
        } else {
            $mensaje = "⚠️ Las contraseñas no coinciden.";
        }
    } else {
        $mensaje = "⚠️ Completa ambos campos.";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Establecer nueva contraseña</title>
    <link rel="stylesheet" href="../css/registro.css">
      <link rel="shortcut icon" href="/imagenes/logo.jpg" type="image/x-icon">
</head>
<body>

<div class="registro-container">
    <h2>Nueva Contraseña</h2>

    <?php if (!empty($mensaje)): ?>
        <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="nueva">Nueva contraseña:</label>
        <input type="password" name="nueva" id="nueva" required>

        <label for="repite">Confirmar contraseña:</label>
        <input type="password" name="repite" id="repite" required>

        <button type="submit">Guardar contraseña</button>
    </form>

    <div class="enlaces-extra">
        <a href="/mvc/login.php">Volver al inicio de sesión</a>
    </div>
</div>

</body>
</html>
