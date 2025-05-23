<?php
// Obtener las variables de entorno de Railway
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$database = getenv('MYSQL_DATABASE');
$port = getenv('MYSQLPORT');

// Depuración: Mostrar los valores de las variables de entorno
echo "Host: " . ($host ?: "No definido") . "<br>";
echo "User: " . ($user ?: "No definido") . "<br>";
echo "Password: " . ($password ?: "No definido") . "<br>";
echo "Database: " . ($database ?: "No definido") . "<br>";
echo "Port: " . ($port ?: "No definido") . "<br>";

// Crear la conexión
$conexion = new mysqli($host, $user, $password, $database, $port);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

echo "Conexión exitosa a la base de datos";
?>