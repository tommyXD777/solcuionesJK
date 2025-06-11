<?php
session_start();
require_once '../conexion.php'; // Usa la conexión correcta de Railway

// Consulta SQL
$sql = "SELECT nombre, apellido, telefono, username, correo FROM clientes";
$resultado = $conexion->query($sql);
$usuarios = [];

if ($resultado && $resultado->num_rows > 0) {
    $usuarios = $resultado->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="/imagenes/logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="/css/tabla_usuarios.css">
    <title>Usuarios Registrados</title>
</head>
<body>
<div class="contenedor-usuarios">
    <a href="adm.php" class="link-volver">Volver al inicio</a>
    <h2>Usuarios Registrados</h2>

    <?php if (empty($usuarios)): ?>
        <p style="text-align: center; color: #f1c40f;">No hay usuarios registrados.</p>
    <?php endif; ?>

    <table class="tabla-usuarios">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Nombre de Usuario</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $user): ?>
            <tr>
                <td data-label="Nombre"><?= htmlspecialchars($user['nombre']) ?></td>
                <td data-label="Apellido"><?= htmlspecialchars($user['apellido']) ?></td>
                <td data-label="Teléfono"><?= htmlspecialchars($user['telefono']) ?></td>
                <td data-label="Correo"><?= htmlspecialchars($user['correo']) ?></td>
                <td data-label="Nombre de Usuario"><?= htmlspecialchars($user['username']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
$conexion->close();
?>
