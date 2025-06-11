<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Verificar Código</title>
</head>
<body>
  <h2>Introduce tu código</h2>
  <form method="POST">
      <label for="codigo">Código:</label>
      <input type="text" name="codigo" required>
      <button type="submit">Verificar</button>
  </form>

<?php
require '../conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'] ?? '';
    if ($codigo) {
        // Buscar si un token que empiece con ese código es válido
        $stmt = $conexion->prepare("SELECT * FROM tokens WHERE token LIKE CONCAT(?, '%') AND type = 'reset' AND is_used = 0 AND expires_at > NOW()");
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $row = $resultado->fetch_assoc();
            // Redirigir a nueva_contraseña.php pasando el token completo
            header("Location: nueva_contraseña.php?token=" . $row['token']);
            exit;
        } else {
            echo "<p>❌ Código inválido o expirado.</p>";
        }
    }
}
?>
</body>
</html>
