<?php
require 'conexion.php';
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Contador de productos en el carrito (para clientes)
$carrito_total = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'cliente' && isset($_SESSION['carrito']))
    ? count($_SESSION['carrito'])
    : 0;

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
    <style>

.servicio-img {
    width: 100%;
    height: 260px;                /* 🔺 Altura aumentada */
    object-fit: contain;          /* 🔒 No recorta los logos */
    object-position: center;
    background-color: white;      /* ✅ Fondos con transparencia */
    border-radius: 12px;
    margin-bottom: 10px;
    transition: transform 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);

}

.servicio-img:hover {
    transform: scale(1.03);
}


</style>

    <style>
        .carrito-badge {
            background-color: red;
            color: white;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 50%;
            position: relative;
            top: -10px;
            left: -10px;
        }
    </style>
    <style>
    main.grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .card {
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.2s;
        text-align: center;
        padding: 16px;
    }

    .card:hover {
        transform: scale(1.02);
    }



    .estado {
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
    }

    .estado.disponible {
        background-color: #d1fae5;
        color: #065f46;
    }

    .estado.agotado {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .card button {
        margin-top: 10px;
    }
</style>

</head>
<body>
<header class="bg-dark text-white text-center py-4">
    <div class="container">
        <img src="/imagenes/logo.jpg" alt="Logo Soluciones JK" class="logo mb-2" style="width: 100px;">
        <h1>Soluciones JK</h1>
        <p>Consulta los mejores precios de tus plataformas favoritas</p>
        <nav class="d-flex justify-content-center gap-3 mt-3">

            <?php if (!isset($_SESSION['usuario'])): ?>
                <a href="/mvc/login.php" class="btn btn-outline-light">Iniciar sesión</a>

            <?php elseif ($_SESSION['rol'] === 'cliente'): ?>
                <a href="/mvc/perfil.php" class="btn btn-outline-light">👤 Perfil</a>
                <a href="/carrito/ver_carrito.php" class="btn btn-outline-light position-relative">
                    🛒 Carrito
                    <?php if ($carrito_total > 0): ?>
                        <span class="carrito-badge"><?= $carrito_total ?></span>
                    <?php endif; ?>
                </a>
                <a href="/logout.php" class="btn btn-outline-light">Cerrar sesión</a>

            <?php elseif ($_SESSION['rol'] === 'administrador'): ?>
                <a href="/administrador/adm.php" class="btn btn-outline-light">📋 Panel de administración</a>
                <a href="/logout.php" class="btn btn-outline-light">Cerrar sesión</a>
            <?php endif; ?>

        </nav>
    </div>
</header>

<main class="grid">
    <?php while ($row = $resultado->fetch_assoc()): ?>
    <div class="card">
        <img src="<?= htmlspecialchars($row['imagen']) ?>" alt="<?= htmlspecialchars($row['nombre_servicio']) ?>" class="servicio-img">

        <h3><?= htmlspecialchars($row['nombre_servicio']) ?></h3>
        <p>Precio: <strong>$<?= number_format($row['precio'], 2) ?></strong></p>

        <p>
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
