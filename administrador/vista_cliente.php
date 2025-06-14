<?php
session_start();
require_once '../conexion.php';

// Consulta SQL (ajustada a tu tabla actual)
$sql = "SELECT correo, username, tipo FROM clientes";
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
    <a href="adm.php" class="link-volver">← Volver al inicio</a>
    <h2>Usuarios Registrados</h2>

    <?php if (empty($usuarios)): ?>
        <p style="text-align: center; color: #f1c40f;">No hay usuarios registrados.</p>
    <?php endif; ?>

    <table class="tabla-usuarios">
        <thead>
            <tr>
                <th>Correo</th>
                <th>Nombre de Usuario</th>
                <th>Tipo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $user): ?>
            <tr>
                <td data-label="Correo"><?= htmlspecialchars($user['correo']) ?></td>
                <td data-label="Nombre de Usuario"><?= htmlspecialchars($user['username']) ?></td>
                <td data-label="Tipo"><?= htmlspecialchars($user['tipo']) ?></td>
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
