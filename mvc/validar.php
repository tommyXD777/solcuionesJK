<?php
session_start();
include("../conexion.php");



$usuario = $_POST['usuario'];
$clave = $_POST['clave'];

// Escapar para evitar inyecciones SQL
$usuario = $conexion->real_escape_string($usuario);
$clave = $conexion->real_escape_string($clave);

// Consulta para buscar el usuario
$sql = "SELECT * FROM usuarios WHERE username='$usuario' AND contraseña='$clave'";

$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $_SESSION['usuario'] = $usuario;
    header("Location: /administrador/adm.php");
} else {
    echo "Usuario o contraseña incorrectos.";
}
?>
