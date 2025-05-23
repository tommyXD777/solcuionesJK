<?php
require '../conexion.php';

$id = $_POST['id'];
$nuevo_precio = $_POST['nuevo_precio'];

$sql = "UPDATE servicios SET precio = ? WHERE id = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("❌ Error en prepare(): " . $conexion->error);
}

$stmt->bind_param("di", $nuevo_precio, $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "✅ Precio actualizado correctamente.";
} else {
    echo "⚠️ No se actualizó el precio. Puede que sea el mismo valor.";
}

$stmt->close();
$conexion->close();
?>
