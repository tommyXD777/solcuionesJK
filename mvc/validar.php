<?php
session_start();
require_once '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';

    // Buscar en administradores: solo por username
    $stmt = $conexion->prepare(
        "SELECT id, username, contraseña FROM administradores 
         WHERE username = ? LIMIT 1"
    );
    if (!$stmt) {
        die("Error en consulta de administradores: " . $conexion->error);
    }
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();
        if (password_verify($clave, $fila['contraseña'])) {
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['usuario'] = $fila['username'];
            $_SESSION['nombre'] = $fila['username']; // Mostrar como nombre en el index
            $_SESSION['rol'] = 'administrador';
            header("Location: /administrador/adm.php");
            exit();
        }
    }

    // Buscar en clientes: por correo o username
    $stmt = $conexion->prepare(
        "SELECT id, correo, username, nombre, contraseña_hash FROM clientes 
         WHERE correo = ? OR username = ? LIMIT 1"
    );
    if (!$stmt) {
        die("Error en consulta de clientes: " . $conexion->error);
    }
    $stmt->bind_param("ss", $usuario, $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();
        if (password_verify($clave, $fila['contraseña_hash'])) {
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['usuario'] = $fila['correo']; // O puedes usar $fila['username']
            $_SESSION['nombre'] = $fila['nombre'];
            $_SESSION['rol'] = 'cliente';
            header("Location: /index.php");
            exit();
        }
    }

    // Si ninguna autenticación fue exitosa
    $_SESSION['error'] = "⚠️ Usuario o contraseña incorrectos.";
    header("Location: login.php");
    exit();
} else {
    echo "Acceso no permitido.";
}
?>
