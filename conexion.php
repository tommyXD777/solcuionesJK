<?php
$host = "localhost";      // Servidor local
$usuario = "root";        // Usuario por defecto en XAMPP
$contrasena = "";         // Contraseña vacía por defecto
$base_de_datos = "servicios_db"; // Cambia por el nombre de tu base

// Crear la conexión
$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "✅ Conexión exitosa a la base de datos.";
}
?>
