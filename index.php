<?php
require 'conexion.php';
session_start();
$resultado = $conexion->query("SELECT * FROM servicios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Soluciones JK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/imagenes/logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

<main class="grid">
    <?php while ($row = $resultado->fetch_assoc()): ?>
        <div class="card">
            <img src="/imagenes/<?= htmlspecialchars($row['imagen']) ?>" alt="<?= htmlspecialchars($row['nombre_servicio']) ?>">
            <h3><?= htmlspecialchars($row['nombre_servicio']) ?></h3>
            <p>Precio: $<?= number_format($row['precio'], 2) ?></p>
            <p>Estado: 
                <span class="estado <?= strtolower(trim($row['estado'])) == 'disponible' ? 'disponible' : 'agotado' ?>">
                    <?= htmlspecialchars($row['estado']) ?>
                </span>
            </p>

            <?php if (strtolower(trim($row['estado'])) == 'disponible'): ?>
                <form method="POST" action="/carrito/agregar_carrito.php">
                    <input type="hidden" name="id_servicio" value="<?= $row['id'] ?>">
                    <button type="submit" class="btn btn-primary mt-2">Agregar al carrito 🛒</button>
                </form>
            <?php else: ?>
                <p class="text-danger">Producto no disponible</p>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</main>

<!-- Widget de contacto -->
<style>
    .contact-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }

    .contact-card {
        display: none;
        padding: 10px;
        border-radius: 10px;
        background: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .contact-card.show {
        display: block;
    }

    .message-icon {
        font-size: 30px;
        cursor: pointer;
        background: #007bff;
        color: white;
        border-radius: 50%;
        padding: 10px;
    }

    .social-buttons a {
        display: block;
        margin: 5px 0;
    }
</style>

<div class="contact-widget">
    <span class="message-icon" id="toggleContactCard">💬</span>
    <div class="contact-card" id="contactCard">
        <div class="social-buttons">
            <a href="https://wa.me/573118113650?text=Hola,%20quiero%20consultar%20precios,%20por%20favor." target="_blank" class="btn btn-success">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            <a href="https://www.facebook.com/share/1FbntxupsP/" target="_blank" class="btn btn-primary">
                <i class="fab fa-facebook-f"></i> Facebook
            </a>
            <a href="https://www.instagram.com/solucionesjk_09?igsh=ZXAxb2hudzBwaXli" target="_blank" class="btn btn-danger" style="background-color: #E1306C; border-color: #E1306C;">
                <i class="fab fa-instagram"></i> Instagram
            </a>
        </div>
    </div>
</div>

<script>
    const toggleIcon = document.getElementById("toggleContactCard");
    const contactCard = document.getElementById("contactCard");

    toggleIcon.addEventListener("click", function (e) {
        e.stopPropagation();
        contactCard.classList.toggle("show");
    });

    // Cerrar la tarjeta si se hace clic fuera
    document.addEventListener("click", function (e) {
        if (!contactCard.contains(e.target) && !toggleIcon.contains(e.target)) {
            contactCard.classList.remove("show");
        }
    });
</script>

<footer class="bg-dark text-white text-center py-3">
    <p>© 2025 Soluciones JK - Distribuidor de Streaming</p>
</footer>
</body>
</html>
