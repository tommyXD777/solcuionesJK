<?php
session_start();
require '../conexion.php';
$id = $_GET['id'] ?? null;

if ($id && isset($_SESSION['carrito'][$id])) {
    unset($_SESSION['carrito'][$id]);
}

header("Location: ver_carrito.php");
exit;
?>
