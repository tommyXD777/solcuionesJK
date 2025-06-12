<?php
require '../conexion.php';
header('Content-Type: application/json');

$nombre = $_POST['nombre_servicio'] ?? '';
$precio = floatval($_POST['precio'] ?? 0);
$imagen = $_FILES['imagen']['name'] ?? '';
$rutaTemporal = $_FILES['imagen']['tmp_name'] ?? '';

if (empty($nombre) || $precio <= 0 || empty($imagen)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Nombre, precio e imagen son requeridos.'
    ]);
    exit;
}

// 🌐 Ruta relativa para Railway (usa /tmp para archivos temporales)
$rutaDestino = '../imagenes/' . basename($imagen);


// Verificar tipo MIME válido
$tipoImagen = mime_content_type($rutaTemporal);
if (!in_array($tipoImagen, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Solo se permiten imágenes JPEG, PNG, GIF o WEBP.'
    ]);
    exit;
}

// ✅ Mover la imagen a /tmp (Railway no permite escribir en otras rutas)
if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al guardar la imagen en /tmp.'
    ]);
    exit;
}

// 💾 Guardar solo el nombre, no el path absoluto (usualmente usas /imagenes en producción local)
$sql = "INSERT INTO servicios (nombre_servicio, precio, imagen, estado) VALUES (?, ?, ?, 'Disponible')";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sds", $nombre, $precio, $imagen);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => '✅ Servicio agregado correctamente'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => '❌ Error al guardar el servicio: ' . $stmt->error
    ]);
}

$stmt->close();
$conexion->close();
