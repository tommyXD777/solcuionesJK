<?php
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_NAME');
$port = getenv('DB_PORT');

// Crear la conexión
$conexion = new mysqli($host, $user, $password, $database, intval($port));

// Verificar conexión
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

echo "✅ Conexión exitosa a la base de datos";
?>
