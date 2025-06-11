<?php
// Conexión a la base de datos
session_start();

$servername = "localhost";
$username = "root"; // Cambia según tu configuración
$password = "";     // Cambia según tu configuración
$database = "servicios_db"; // Cambia esto

$conn = new mysqli($servername, $username, $password, $database);

// Verifica conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta SQL
$sql = "SELECT nombre, apellido, telefono,username, correo FROM clientes";
$resultado = $conn->query($sql);
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
                <th>Nombre de Usuario</th> <!-- ✅ Nueva columna -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $user): ?>
            <tr>
                <td data-label="Nombre"><?= htmlspecialchars($user['nombre']) ?></td>
                <td data-label="Apellido"><?= htmlspecialchars($user['apellido']) ?></td>
                <td data-label="Teléfono"><?= htmlspecialchars($user['telefono']) ?></td>
                <td data-label="Correo"><?= htmlspecialchars($user['correo']) ?></td>
                <td data-label="Nombre de Usuario"><?= htmlspecialchars($user['username']) ?></td> <!-- ✅ Nueva celda -->
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
$conn->close();
?>
