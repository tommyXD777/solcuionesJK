<?php
require '../conexion.php';
header('Content-Type: application/json');

$nombre = $_POST['nombre_servicio'] ?? '';
$precio = floatval($_POST['precio'] ?? 0);
$imagen = $_FILES['imagen']['name'] ?? '';
$rutaTemporal = $_FILES['imagen']['tmp_name'] ?? '';

// Validación básica
if (empty($nombre) || $precio <= 0 || empty($imagen)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Nombre, precio e imagen son requeridos.'
    ]);
    exit;
}

// Verificar tipo MIME válido
$tipoImagen = mime_content_type($rutaTemporal);
if (!in_array($tipoImagen, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Solo se permiten imágenes JPEG, PNG, GIF o WEBP.'
    ]);
    exit;
}

// ======= FUNCIÓN PARA SUBIR A CLOUDINARY =======
function subirACloudinary($archivoTemporal) {
    $cloud_name = 'dsrzx5q0r'; // ← reemplaza si usas otro
    $upload_preset = 'ml_default'; // este preset viene por defecto
    $url = "https://api.cloudinary.com/v1_1/$cloud_name/image/upload";

    $post = [
        'file' => new CURLFile($archivoTemporal),
        'upload_preset' => $upload_preset
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $respuesta = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($respuesta, true);
    return $data['secure_url'] ?? null;
}

// Subimos a Cloudinary
$urlImagen = subirACloudinary($rutaTemporal);

if (!$urlImagen) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al subir la imagen a Cloudinary'
    ]);
    exit;
}

// Guardamos en la base de datos la URL de Cloudinary
$sql = "INSERT INTO servicios (nombre_servicio, precio, imagen, estado) VALUES (?, ?, ?, 'Disponible')";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sds", $nombre, $precio, $urlImagen);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => '✅ Servicio agregado correctamente con imagen subida a Cloudinary'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => '❌ Error al guardar el servicio en la base de datos'
    ]);
}

$stmt->close();
$conexion->close();
