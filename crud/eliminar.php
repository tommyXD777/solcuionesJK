<?php
require '../conexion.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? 0;

if (!is_numeric($id) || $id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID de servicio inválido.'
    ]);
    exit;
}

$sql = "DELETE FROM servicios WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Servicio eliminado correctamente.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al eliminar: ' . $stmt->error
    ]);
}

$stmt->close();
$conexion->close();
?>