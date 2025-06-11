<?php
session_start();

// ✅ Validar sesión antes de agregar al carrito
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: /mvc/login.php"); // Cambia la ruta si tu login está en otra parte
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_servicio'];
  $cantidad = 1; // Siempre se agrega una sola pantalla


    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    if (isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id] += $cantidad;
    } else {
        $_SESSION['carrito'][$id] = $cantidad;
    }

    header("Location: ../index.php");
    exit;
}
?>
