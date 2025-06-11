<?php
session_start();
require '../conexion.php';

$carrito = $_SESSION['carrito'] ?? [];

// Mostrar mensaje si el carrito está vacío
if (empty($carrito)) {
    echo "<h2>🛒 Tu carrito está vacío</h2>";
    echo '<div class="text-center mt-4">
            <a href="/index.php" class="btn btn-secondary">🔙 Volver al inicio</a>
          </div>';
    exit;
}

// Obtener los productos del carrito
$ids = implode(',', array_keys($carrito));
$sql = "SELECT * FROM servicios WHERE id IN ($ids)";
$resultado = $conexion->query($sql);

if (!$resultado || $resultado->num_rows === 0) {
    echo "<h2>Error al cargar los productos del carrito.</h2>";
    exit;
}

// Guardar los productos para uso doble
$productos = [];
while ($row = $resultado->fetch_assoc()) {
    $productos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link rel="shortcut icon" href="/imagenes/logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="/css/carrito.css">

    
</head>
<body>
<div class="carrito-container">


<h2 class="mb-4">🛒 Carrito de Compras</h2>

<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Servicio</th>
            <th>Precio</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total = 0;
        foreach ($productos as $row):
            $id = $row['id'];
            $cantidad = $carrito[$id];
            $subtotal = $row['precio'] * $cantidad;
            $total += $subtotal;
        ?>
        <tr>
            <td><?= htmlspecialchars($row['nombre_servicio']) ?></td>
            <td>$<?= number_format($row['precio'], 2) ?></td>
            <td><a href="quitar_carrito.php?id=<?= $id ?>" class="btn btn-sm btn-danger">❌ Quitar</a></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td><strong>Total</strong></td>
            <td colspan="2"><strong>$<?= number_format($total, 2) ?></strong></td>
        </tr>
    </tbody>
</table>

<?php
// Construir mensaje limpio para WhatsApp
$nombres = [];
foreach ($productos as $producto) {
    $id = $producto['id'];
    if (isset($carrito[$id]) && $carrito[$id] > 0) {
        $nombres[] = $producto['nombre_servicio'];
    }
}

$lista = implode(', ', $nombres);
$mensaje = "Hola! Quiero realizar el siguiente pedido: $lista. Gracias. Estoy listo para confirmar.";

$mensajeCodificado = urlencode($mensaje);
$telefono = "573011551141";
$whatsappURL = "https://wa.me/$telefono?text=$mensajeCodificado";
?>

<!-- Botón de WhatsApp -->
<div class="mt-4">
    <a href="/carrito/enviar_pedido.php" class="btn btn-success">
        Finalizar compra vía WhatsApp 💬
    </a>
</div>

<!-- Botón Volver al inicio -->
<div class="mt-3">
    <a href="/index.php" class="btn btn-secondary">🔙 Volver al inicio</a>
</div> <!-- Fin de carrito-container -->
</body>

</html>
