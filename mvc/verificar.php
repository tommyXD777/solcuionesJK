<?php
require '../conexion.php';
session_start();

$token = $_GET['token'] ?? '';
$tipo = $_GET['type'] ?? '';
$mensaje = "";

if (!$token || !$tipo) {
    exit("❌ Parámetros inválidos.");
}

$stmt = $conexion->prepare("SELECT user_id FROM tokens 
    WHERE token = ? AND type = ? AND is_used = 0 AND expires_at > NOW()");
$stmt->bind_param("ss", $token, $tipo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    exit("❌ Token inválido, expirado o ya fue usado.");
}

$datos = $resultado->fetch_assoc();
$user_id = $datos['user_id'];

// Si es validación de cuenta (tipo register)
if ($tipo === 'register') {
    // (Opcional) Aquí puedes marcar como verificado si tienes un campo
    // $conexion->prepare("UPDATE clientes SET verificado = 1 WHERE id = ?")->execute([$user_id]);

    // Marcar token como usado
    $stmt = $conexion->prepare("UPDATE tokens SET is_used = 1 WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();

    $mensaje = "✅ Tu cuenta ha sido verificada correctamente. Ahora puedes iniciar sesión.";
}

// Si es recuperación de contraseña
elseif ($tipo === 'reset') {
    // Redirigir al formulario para nueva contraseña
    header("Location: nueva_contraseña.php?token=$token");
    exit;
} else {
    exit("❌ Tipo de acción no reconocido.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación</title>
    <link rel="stylesheet" href="../css/registro.css">
</head>
<body>

<div class="registro-container">
    <h2>Verificación</h2>

    <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>

    <div class="enlaces-extra">
        <a href="/mvc/login.php">Ir al inicio de sesión</a>
    </div>
</div>

</body>
</html>
