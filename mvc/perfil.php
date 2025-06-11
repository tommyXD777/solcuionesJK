<?php
require '../conexion.php';
session_start();

// Validar sesión del cliente o admin
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['cliente', 'admin'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

// Consulta según el rol
if ($rol === 'cliente') {
    $sql = "SELECT nombre, apellido, telefono, correo,username FROM clientes WHERE id = ?";
} else {
    $sql = "SELECT nombre AS nombre, '' AS apellido, '' AS telefono, correo, username FROM administradores WHERE id = ?";
}

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error en prepare(): " . $conexion->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <link rel="shortcut icon" href="/imagenes/logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../css/perfil.css">
</head>
<body>
    <div class="perfil-container">
        <h2>Mi Perfil</h2>
        <form method="POST" action="actualizar_perfil.php">
            <div class="back-to-home">
                <a href="/index.php" class="link-volver">← Volver al inicio</a>
            </div>

            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>

            <?php if ($rol === 'cliente'): ?>
                <label>Apellido:</label>
                <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>

                <label>Teléfono:</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>" required>
            <?php endif; ?>

            <label>Correo:</label>
            <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
            <label>Nombre de usuario:</label>
<input type="text" name="username" value="<?= htmlspecialchars($usuario['username']) ?>" required>


                   <script>
document.querySelector('form').addEventListener('submit', function(e) {
    const pass1 = document.getElementById('nueva_contrasena').value;
    const pass2 = document.getElementById('confirmar_contrasena').value;

    if (pass1 && pass1 !== pass2) {
        alert("❌ Las contraseñas no coinciden.");
        e.preventDefault();
    }
});
</script>

            <label>Nueva Contraseña (opcional):</label>
<input type="password" name="nueva_contrasena" id="nueva_contrasena">

<label>Confirmar Nueva Contraseña:</label>
<input type="password" name="confirmar_contrasena" id="confirmar_contrasena">

            <button type="submit" class="btn-actualizar">Actualizar Perfil</button>
        </form>
    </div>
</body>
</html>
