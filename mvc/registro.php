<?php
require '../conexion.php';
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mensaje = "";
$registro_exitoso = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST['correo'] ?? '';
    $clave = $_POST['clave'] ?? '';

    if ($correo && $clave) {
        // Verificar si el correo ya está registrado
        $stmt = $conexion->prepare("SELECT id FROM clientes WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $mensaje = "❌ El correo ya está registrado.";
        } else {
            // Insertar nuevo usuario
            $clave_hash = password_hash($clave, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("INSERT INTO clientes (correo, contraseña_hash) VALUES (?, ?)");
            $stmt->bind_param("ss", $correo, $clave_hash);

            if ($stmt->execute()) {
                $_SESSION['registro_exitoso'] = true;
                header("Location: login.php");
                exit;
            } else {
                $mensaje = "❌ Error al registrar: " . $stmt->error;
            }
        }
    } else {
        $mensaje = "⚠️ Completa todos los campos.";
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
        <label>Correo:</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" required>

        <label>Contraseña:</label>
        <input type="password" name="clave" required>

        <button type="submit" name="registrar" class="btn-registrar">Registrarse</button>
        <a href="/mvc/login.php" class="btn-volver">← Volver al inicio de sesión</a>
    </form>
</div>

</body>
</html>
