<?php
session_start();
require '../conexion.php';

$carrito = $_SESSION['carrito'] ?? [];

if (empty($carrito)) {
    echo "Tu carrito está vacío.";
    exit;
}

$ids = implode(',', array_keys($carrito));
$consulta = $conexion->query("SELECT * FROM servicios WHERE id IN ($ids)");

$nombres = [];
while ($producto = $consulta->fetch_assoc()) {
    $id = $producto['id'];
    if (isset($carrito[$id]) && $carrito[$id] > 0) {
        $nombres[] = $producto['nombre_servicio'];
    }
}

$lista = implode(', ', $nombres);
$mensaje = "Hola! Quiero realizar el siguiente pedido: $lista. Gracias. Estoy listo para confirmar.";
$mensajeCodificado = urlencode($mensaje);

// Limpiar el carrito
unset($_SESSION['carrito']);

$telefono = "573118113650";
$whatsappURL = "https://wa.me/$telefono?text=$mensajeCodificado";

// Redirigir a WhatsApp
header("Location: $whatsappURL");
exit;
?>
