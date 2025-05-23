<?php
require '../conexion.php';

$id = $_POST['id'];

// Obtener estado actual
$sql = "SELECT estado FROM servicios WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($estado_actual);
$stmt->fetch();
$stmt->close();

// Cambiar estado
$estado_actual = strtolower(trim($estado_actual));
$nuevo_estado = ($estado_actual === 'disponible') ? 'agotado' : 'disponible';

$update = $conexion->prepare("UPDATE servicios SET estado = ? WHERE id = ?");
$update->bind_param("si", $nuevo_estado, $id);
$update->execute();
header("Location: ../administrador/adm.php");

?>
