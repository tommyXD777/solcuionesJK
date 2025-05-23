<?php
require '../conexion.php';

header('Content-Type: application/json');

// Obtener datos del formulario
$nombre = $_POST['nombre_servicio'] ?? '';
$precio = $_POST['precio'] ?? 0;
$imagen = $_FILES['imagen']['name'] ?? '';
$rutaTemporal = $_FILES['imagen']['tmp_name'] ?? '';

// Validar datos
if (empty($nombre) || !is_numeric($precio) || empty($imagen)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Nombre, precio e imagen son requeridos.'
    ]);
    exit;
}

// Definir la ruta de destino en la raíz
$rutaDestino = __DIR__ . '/../imagenes/' . basename($imagen);

// Asegúrate de que la carpeta 'imagenes' exista en la raíz
if (!is_dir(__DIR__ . '/../imagenes')) {
    mkdir(__DIR__ . '/../imagenes', 0777, true);
}

// Verificar si el archivo es una imagen válida
$tipoImagen = mime_content_type($rutaTemporal);
if (!in_array($tipoImagen, ['image/jpeg', 'image/png', 'image/gif'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Solo se permiten imágenes JPEG, PNG o GIF.'
    ]);
    exit;
}

// Mover la imagen a la carpeta en la raíz
if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al guardar la imagen.'
    ]);
    exit;
}

// Insertar en la base de datos
$sql = "INSERT INTO servicios (nombre_servicio, precio, imagen, estado) VALUES (?, ?, ?, 'Disponible')";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sds", $nombre, $precio, $imagen);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Servicio agregado correctamente'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al guardar el servicio: ' . $stmt->error
    ]);
}

$stmt->close();
$conexion->close();
?>