<?php
// Obtener las variables de entorno de Railway
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$database = getenv('MYSQL_DATABASE');
$port = getenv('MYSQLPORT');

// Crear la conexión
$conexion = new mysqli($host, $user, $password, $database, $port);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

echo "Conexión exitosa a la base de datos";
?>