<?php
session_start();
$id      = $_GET['id'] ?? '';
$usuario = $_SESSION['usuari'] ?? null;
if (!$id) { header('Location: productes.php'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/logo.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/productes.css">
    <link rel="stylesheet" href="css/producto.css">
    <script type="importmap">
    {
        "imports": {
            "axios": "https://cdn.jsdelivr.net/npm/axios@1.9.0/+esm"
        }
    }
    </script>
    <title>Producto · KoffTea</title>
    <script src="js/api-check.js"></script>
</head>
<body>

<a class="skip-link" href="#main">Saltar al contenido</a>

<header class="header">
    <div class="logo">
        <a href="index.php" aria-label="KoffTea - Inicio">
            <img src="images/logo.png" alt="KoffTea">
        </a>
    </div>

    <nav class="main-nav" id="main-nav" aria-label="Navegación principal">
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="productes.php" aria-current="page">Productos</a></li>
            <li><a href="formulario.php">Contacto</a></li>
        </ul>
    </nav>

    <form class="search-bar" role="search" onsubmit="handleSearch(event)">
        <label for="search-input" class="visually-hidden">Buscar productos</label>
        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
        <input type="search" id="search-input" name="q" placeholder="Buscar cafés o tés...">
    </form>

    <div class="header-right">
        <nav class="nav-icons" aria-label="Acciones de usuario">
            <ul>
                <li>
                    <a href="auth/profile.php" aria-label="Perfil de usuario">
                        <i class="fa-solid fa-user" aria-hidden="true"></i>
                    </a>
                </li>
                <li class="wishlist-wrapper">
                    <button class="wishlist-btn" aria-label="Lista de deseos" aria-expanded="false">
                        <i class="fa-regular fa-heart" aria-hidden="true"></i>
                        <span class="wishlist-badge" id="wishlist-badge" hidden>0</span>
                    </button>
                </li>
                <li class="cart-wrapper">
                    <button class="cart-btn" aria-label="Carrito de compra" aria-expanded="false">
                        <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
                        <span class="cart-badge" id="cart-badge" hidden>0</span>
                    </button>
                </li>
            </ul>
        </nav>
        <button class="hamburger" aria-label="Abrir menú" aria-expanded="false" aria-controls="main-nav">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- Mini-lista de deseos -->
<div class="mini-cart" id="mini-wishlist" role="dialog" aria-label="Lista de deseos" hidden>
    <div class="mini-cart-header"><i class="fa-solid fa-heart"></i> Lista de deseos</div>
    <div class="mini-cart-items" id="mini-wishlist-items"></div>
    <div class="mini-cart-footer">
        <span><strong id="mini-wishlist-count">0 productos</strong></span>
    </div>
</div>

<!-- Mini-carrito -->
<div class="mini-cart" id="mini-cart" role="dialog" aria-label="Carrito de compra" hidden>
    <div class="mini-cart-header"><i class="fa-solid fa-cart-shopping"></i> Tu carrito</div>
    <div class="mini-cart-items" id="mini-cart-items"></div>
    <div class="mini-cart-footer">
        <span>Total: <strong id="mini-cart-total">0,00 €</strong></span>
        <a href="#" class="btn">Comprar</a>
    </div>
</div>

<main id="main">

<!-- Breadcrumb -->
<nav class="breadcrumb" aria-label="Ruta de navegación">
    <a href="index.php">Inicio</a>
    <span aria-hidden="true">›</span>
    <a href="productes.php">Productos</a>
    <span aria-hidden="true">›</span>
    <span id="breadcrumb-nombre" aria-current="page"></span>
</nav>

    <!-- Detalle producto -->
    <div class="producto-detalle">
        <div class="producto-img-wrap">
            <img id="producto-img" src="" alt="">
        </div>

        <div class="producto-info">
            <span class="producto-badge" id="producto-badge"></span>
            <h1 class="producto-nombre" id="producto-nombre"></h1>

            <p class="producto-stars">
                <span id="avg-stars">☆☆☆☆☆</span>
                <span id="avg-count"></span>
            </p>

            <p class="producto-precio" id="producto-precio"></p>

            <div class="producto-meta">
                <span><i class="fa-solid fa-location-dot"></i> <span id="producto-origen"></span></span>
                <span><i class="fa-solid fa-box"></i> <span id="producto-formato"></span></span>
                <span>
                    <i class="fa-solid fa-fire-flame-curved"></i>
                    Intensidad: <span class="intensidad-dots" id="producto-intensidad"></span>
                </span>
                <span id="producto-stock"></span>
            </div>

            <p class="producto-desc" id="producto-desc" hidden></p>

            <div class="detalle-actions">
                <button class="btn-cart-detalle" id="btn-cart-detalle" disabled>
                    <i class="fa-solid fa-spinner fa-spin"></i> Cargando...
                </button>
                <button class="btn-wishlist-detalle" id="btn-wishlist-detalle">
                    <i class="fa-regular fa-heart"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Comentarios -->
    <section class="comentarios-section">
        <h2><i class="fa-regular fa-comments"></i> Opiniones del producto</h2>

        <?php if ($usuario): ?>
        <div class="comentario-form">
            <h3>Deja tu opinión</h3>
            <div class="star-selector" role="radiogroup" aria-label="Valoración">
                <input type="radio" id="star5" name="valoracion" value="5">
                <label for="star5" title="5 estrellas">★</label>
                <input type="radio" id="star4" name="valoracion" value="4">
                <label for="star4" title="4 estrellas">★</label>
                <input type="radio" id="star3" name="valoracion" value="3">
                <label for="star3" title="3 estrellas">★</label>
                <input type="radio" id="star2" name="valoracion" value="2">
                <label for="star2" title="2 estrellas">★</label>
                <input type="radio" id="star1" name="valoracion" value="1">
                <label for="star1" title="1 estrella">★</label>
            </div>
            <textarea id="comentario-texto" placeholder="Escribe tu opinión sobre este producto..." rows="3"></textarea>
            <button id="btn-publicar" class="btn-publicar" onclick="submitComment()">
                <i class="fa-solid fa-paper-plane"></i> Publicar
            </button>
            <p id="form-msg" class="msg-form"></p>
        </div>
        <?php else: ?>
        <p class="login-prompt">
            <i class="fa-solid fa-lock"></i>
            <a href="auth/login.php">Inicia sesión</a> para dejar una opinión sobre este producto.
        </p>
        <?php endif; ?>

        <div id="comentarios-lista"></div>
    </section>

</main>

<footer>
    <p>&copy; 2025 KoffTea Times · Inspirando momentos de lectura y aroma.</p>
    <p>
        <a href="formulario.php">Contacto</a> ·
        <a href="productes.php">Productos</a>
    </p>
</footer>

<input type="hidden" id="producto-id" value="">

<script src="js/cart.js"></script>
<script src="js/wishlist.js"></script>
<script src="js/comments.js"></script>
<script type="module" src="js/producto.js"></script>
<script>
function handleSearch(e) {
    e.preventDefault();
    const q = document.getElementById('search-input').value.trim();
    if (q) window.location.href = 'productes.php?q=' + encodeURIComponent(q);
}
</script>
</body>
</html>
