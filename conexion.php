<?php
$host     = getenv('MYSQLHOST');
$user     = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$database = getenv('MYSQL_DATABASE');  // <- aquí corregido
$port     = getenv('MYSQLPORT');

$conexion = new mysqli($host, $user, $password, $database, intval($port));

if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}
?>
