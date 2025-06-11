<?php
// Obtener las variables de entorno definidas por Railway
$host     = getenv('MYSQLHOST');
$user     = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$database = getenv('MYSQLDATABASE');
$port     = getenv('MYSQLPORT');

// Crear la conexión (puerto convertido a entero)
$conexion = new mysqli($host, $user, $password, $database, intval($port));

// Verificar conexión
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}
?>
