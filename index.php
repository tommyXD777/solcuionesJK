<?php
require 'conexion.php';

$resultado = $conexion->query("SELECT * FROM servicios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Soluciones JK</title>
    <link rel="shortcut icon" href="/imagenes/logo.jpg" type="image/x-icon">
  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/serviciosJK/css/style.css">
</head>
<body>
    <header class="bg-dark text-white text-center py-4">
        <div class="container">
            <img src="/imagenes/logo.jpg" alt="Logo Soluciones JK" class="logo mb-2" style="width: 100px;">
            <h1>Soluciones JK</h1>
            <p>Consulta los mejores precios de tus plataformas favoritas</p>
            <nav>
                <a href="/mvc/login.html" class="btn btn-outline-light">Iniciar sesión</a>
            </nav>
        </div>
    </header>

    <main class="catalogo">
        <?php while ($row = $resultado->fetch_assoc()): ?>
            <div class="plataforma">
                <img src="/imagenes/<?= htmlspecialchars($row['imagen']) ?>" alt="<?= htmlspecialchars($row['nombre_servicio']) ?>">
                <h2><?= htmlspecialchars($row['nombre_servicio']) ?></h2>
                <p>Precio: $<?= number_format($row['precio'], 2) ?></p>
<p>Estado: 
<span class="estado <?= strtolower(trim($row['estado'])) == 'disponible' ? 'disponible' : 'no-disponible' ?>">
    <?= htmlspecialchars($row['estado']) ?>
</span>
</p>

            </div>
        <?php endwhile; ?>
    </main>

    <div class="contact-widget fixed-bottom d-flex justify-content-end p-3">
        <div class="card p-3">
            <span class="message-icon">💬 Contáctenos</span>
            <div class="mt-2">
                <a href="https://wa.me/+573118113650" target="_blank" class="btn btn-success me-2">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <a href="https://www.facebook.com/share/1FbntxupsP/" target="_blank" class="btn btn-primary">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2025 Soluciones JK - Distribuidor de Streaming</p>
    </footer>
</body>
</html>