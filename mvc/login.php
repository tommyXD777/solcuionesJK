<?php
session_start();
if (isset($_SESSION['registro_exitoso'])) {
    echo "<p style='color: green;'>✅ Registro exitoso. Ahora puedes iniciar sesión.</p>";
    unset($_SESSION['registro_exitoso']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="/css/login.css"> <!-- Usa tu CSS centralizado -->
      <link rel="shortcut icon" href="/imagenes/logo.jpg" type="image/x-icon">
</head>
<body>
    <div class="login-container">
        <img src="/imagenes/logo.jpg" class="login-logo" alt="Logo" />

        <h1>Iniciar Sesión</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red; font-size: 14px;"><?= $_SESSION['error'] ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="validar.php" method="POST">
            <div class="input-group">
                <label for="usuario">Usuario o correo</label>
                <input type="text" id="usuario" name="usuario" required />
            </div>

            <div class="input-group">
                <label for="clave">Contraseña</label>
                <input type="password" id="clave" name="clave" required />
            </div>

            <button type="submit">Iniciar sesión</button>
        </form>

        <!-- Enlaces extras -->
        <div class="back-to-home">
            <p>¿Olvidaste tu contraseña? <a href="/mvc/recupera.php">Recupérala aquí</a></p>
            <p>¿No tienes cuenta? <a href="/mvc/registro.php">Crear cuenta</a></p>
        </div>
    </div>
</body>
</html>
