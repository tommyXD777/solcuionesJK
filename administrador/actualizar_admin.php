<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: /mvc/login.php");
    exit();
}

$id_admin = $_SESSION['usuario_id'];
$nombre = $_POST['nombre'] ?? '';
$nueva_contrasena = $_POST['nueva_contrasena'] ?? '';
$confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

// Validar entrada
if (empty($nombre)) {
    echo "<script>alert('El nombre no puede estar vacío.'); history.back();</script>";
    exit();
}

if (!empty($nueva_contrasena)) {
    // Validar que las contraseñas coincidan
    if ($nueva_contrasena !== $confirmar_contrasena) {
        echo "<script>alert('❌ Las contraseñas no coinciden.'); history.back();</script>";
        exit();
    }

    $hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
    $stmt = $conexion->prepare("UPDATE administradores SET username = ?, contraseña = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nombre, $hash, $id_admin);
} else {
    $stmt = $conexion->prepare("UPDATE administradores SET username = ? WHERE id = ?");
    $stmt->bind_param("si", $nombre, $id_admin);
}

if ($stmt->execute()) {
    $_SESSION['nombre'] = $nombre;
    header("Location: /administrador/ver_perfil.php?mensaje=actualizado");
    exit();
} else {
    echo "<script>alert('❌ Error al actualizar los datos.'); history.back();</script>";
}
?>
