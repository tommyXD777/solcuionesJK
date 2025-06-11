<?php
require_once '../conexion.php'; // Asegúrate de que esta ruta es correcta
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';

    if (empty($usuario) || empty($clave)) {
        echo "⚠️ Por favor llena todos los campos.";
        exit();
    }

    // Generar el hash de la contraseña
    $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

    // Insertar en la base de datos
    $stmt = $conexion->prepare("INSERT INTO administradores (username, contraseña) VALUES (?, ?)");
    if (!$stmt) {
        die("Error al preparar la consulta: " . $conexion->error);
    }

    $stmt->bind_param("ss", $usuario, $clave_hash);

    if ($stmt->execute()) {
        echo "✅ Administrador registrado con éxito.";
    } else {
        echo "❌ Error al registrar administrador: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
} else {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Administrador</title>
      <link rel="shortcut icon" href="/imagenes/logo.jpg" type="image/x-icon">
</head>
<body>
    <h2>Registrar nuevo administrador</h2>
    
    <form method="POST" action="">
        <label for="usuario">Usuario:</label>
        <input type="text" name="usuario" id="usuario" required><br><br>

        <label for="clave">Contraseña:</label>
        <input type="password" name="clave" id="clave" required><br><br>

        <button type="submit">Registrar</button>
    </form>
</body>
</html>
<?php
}
?>
