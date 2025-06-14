<?php
// Obtener variables desde entorno Railway
$host     = getenv('MYSQLHOST');
$user     = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$database = getenv('MYSQL_DATABASE'); // ✅ nombre exacto
$port     = getenv('MYSQLPORT');

// Crear la conexión
$conexion = new mysqli($host, $user, $password, $database, intval($port));

// Verificar conexión
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}
?>
