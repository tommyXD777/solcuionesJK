<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: /mvc/login.php");
    exit();
}

$id_admin = $_SESSION['usuario_id'];

$stmt = $conexion->prepare("SELECT * FROM administradores WHERE id = ?");
$stmt->bind_param("i", $id_admin);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $admin = $resultado->fetch_assoc();
} else {
    die("No se encontró al administrador.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil del Administrador</title>
      <link rel="shortcut icon" href="/imagenes/logo.jpg" type="image/x-icon">
   <link rel="stylesheet" href="/css/perfil_admin.css">

  
</head>
<body>
   <div class="perfil-container">

        <h3 class="text-center mb-4">Mi Perfil de Administrador</h3>
        <div class="text-center mb-3">
            <a href="/administrador/adm.php" class="link-volver">Volver al Panel</a>
        </div>
        <form method="POST" action="actualizar_admin.php">
            <div class="mb-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Correo:</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" disabled>
            </div>
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
