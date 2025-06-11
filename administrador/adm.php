<?php
require '../conexion.php';
session_start();
$servicios = $conexion->query("SELECT * FROM servicios");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Soluciones JK</title>
    <link rel="shortcut icon" href="/imagenes/logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="/css/adm.css">
</head>
<body>
    <div class="admin-container">
        <h1>Panel de Administrador</h1>
        <div class="back-to-home">
            <a href="/index.php" class="btn-volver">Volver al inicio</a>
            <a href="vista_cliente.php" class="btn-volver">Ver clientes</a>
            <a href="ver_perfil.php" class="btn-volver">perfil</a>
            <a href="/logout.php" class="btn-volver">cerrar sesion</a>
        </div>
        <div class="add-product-form">
            <h2>Agregar Nuevo servicio</h2>
            <div id="mensaje-form" style="margin-bottom: 10px;"></div>
            <form id="form-agregar-servicio" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nombre del servicio</label>
                    <input type="text" name="nombre_servicio" required>
                </div>
                <div class="form-group">
                    <label>Precio ($)</label>
                    <input type="number" name="precio" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Imagen</label>
                    <input type="file" name="imagen" accept="image/*" required>
                </div>
                <button type="submit">Guardar Servicio</button>
            </form>
        </div>
        <h2>Servicios Existentes</h2>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Imagen</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Cambiar Estado</th>
                    <th>Editar Precio</th>
                    <th>Eliminar producto</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $servicios->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nombre_servicio']) ?></td>
                        <td><img src="/imagenes/<?= htmlspecialchars($row['imagen']) ?>" alt="Imagen del servicio" width="100"></td>
                        <td class="celda-precio">$<?= number_format($row['precio'], 2) ?></td>
                        <td><?= $row['estado'] ?></td>
                        <td>
                            <form action="/crud/cambiar_estado.php" method="POST">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <span class="estado <?= strtolower(trim($row['estado'])) == 'disponible' ? 'disponible' : 'no-disponible' ?>">
                                <button type="submit">Cambiar estado</button>
                            </form>
                        </td>
                        <td>
                            <form class="form-actualizar" data-id="<?= $row['id'] ?>">
                                <input type="number" step="0.01" name="nuevo_precio" value="<?= $row['precio'] ?>" required>
                                <button type="submit">Actualizar</button>
                            </form>
                            <div class="mensaje" id="mensaje-<?= $row['id'] ?>"></div>
                        </td>
                        <td>
                            <button onclick="confirmDelete(<?= $row['id'] ?>)" style="text-align: center; margin-left: 2.5rem;">Eliminar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
        document.getElementById('form-agregar-servicio').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('/crud/agregar.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') setTimeout(() => location.reload(), 1000);
                })
                .catch(() => alert('❌ Error al conectar con el servidor.'));
        });

        document.querySelectorAll('.form-actualizar').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const id = this.dataset.id;
                const nuevo_precio = this.querySelector('input[name="nuevo_precio"]').value;
                const mensajeDiv = document.getElementById('mensaje-' + id);
                fetch('/solcuionesJK/crud/editar_precio.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `id=${id}&nuevo_precio=${nuevo_precio}`
                })
                .then(res => res.text())
                .then(data => {
                    mensajeDiv.innerHTML = data;
                    mensajeDiv.style.color = 'green';
                    const precioCell = form.closest('tr').querySelector('.celda-precio');
                    if (precioCell) precioCell.textContent = '$' + parseFloat(nuevo_precio).toFixed(2);
                })
                .catch(() => {
                    mensajeDiv.innerHTML = '❌ Error al actualizar.';
                    mensajeDiv.style.color = 'red';
                });
            });
        });

        function confirmDelete(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este servicio?')) {
                fetch('/crud/eliminar.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `id=${id}`
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') setTimeout(() => location.reload(), 1000);
                })
                .catch(() => alert('❌ Error al conectar con el servidor.'));
            }
        }
    </script>
</body>
</html>
