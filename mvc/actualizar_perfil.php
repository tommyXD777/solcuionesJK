<?php
session_start();
require '../conexion.php';

// Verificación de sesión
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['usuario_id'];

// Recolectar datos del formulario
$correo = $_POST['correo'] ?? '';
$username = $_POST['username'] ?? '';
$nueva_contrasena = $_POST['nueva_contrasena'] ?? '';

// Validar campos obligatorios
if (empty($correo) || empty($username)) {
    echo "<script>alert('⚠️ Por favor, completa todos los campos obligatorios.'); history.back();</script>";
    exit;
}

// Verificar si el username ya está en uso por otro usuario
$stmt = $conexion->prepare("SELECT id FROM clientes WHERE username = ? AND id != ?");
$stmt->bind_param("si", $username, $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    echo "<script>alert('❌ El nombre de usuario ya está en uso por otro usuario.'); history.back();</script>";
    exit;
}

// Preparar UPDATE
if (!empty($nueva_contrasena)) {
    $clave_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
    $sql = "UPDATE clientes SET correo=?, username=?, contraseña_hash=? WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssi", $correo, $username, $clave_hash, $id);
} else {
    $sql = "UPDATE clientes SET correo=?, username=? WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssi", $correo, $username, $id);
}

// Ejecutar y verificar resultado
if ($stmt->execute()) {
    echo "<script>alert('✅ Perfil actualizado correctamente'); window.location='perfil.php';</script>";
} else {
    echo "<script>alert('❌ Error al actualizar el perfil: " . $stmt->error . "'); history.back();</script>";
}
?>
