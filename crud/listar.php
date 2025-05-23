<?php
require '../conexion.php';
$conexion = new mysqli("localhost", "usuario", "contraseña", "servicios_db");
$resultado = $conexion->query("SELECT * FROM productos");
?>

<h2>Lista de productos</h2>
<?php while ($fila = $resultado->fetch_assoc()): ?>
    <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
        <img src="<?= $fila['imagen'] ?>" alt="<?= $fila['nombre'] ?>" width="100"><br>
        <strong><?= $fila['nombre'] ?></strong><br>
        Precio: $<?= $fila['precio'] ?><br>

        <form action="editar_precio.php" method="POST" style="margin-top:10px;">
            <input type="hidden" name="id" value="<?= $fila['id'] ?>">
            <input type="number" name="nuevo_precio" value="<?= $fila['precio'] ?>" step="0.01" required>
            <button type="submit">Actualizar precio</button>
        </form>
    </div>
<?php endwhile; ?>
