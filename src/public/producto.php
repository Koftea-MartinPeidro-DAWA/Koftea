<?php
session_start();

$id       = $_GET['id'] ?? '';
$dataFile = __DIR__ . '/data/productos.json';
$data     = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
$productes = $data['productes'] ?? [];

$producto = null;
foreach ($productes as $p) {
    if ($p['ID'] === $id) { $producto = $p; break; }
}
if (!$producto) { header('Location: productes.php'); exit; }

$catImg = [
    'Grano'    => 'images/category/grano.jpg',
    'Molido'   => 'images/category/molido.jpg',
    'Cápsula'  => 'images/category/capsula.jpg',
    'Cápsulas' => 'images/category/capsula.jpg',
    'Té'       => 'images/category/te.jpg',
    'Te'       => 'images/category/te.jpg',
];

$img      = $catImg[$producto['Categoria']] ?? 'images/category/grano.jpg';
$inStock  = (int)$producto['Stock'] > 0;
$usuario  = $_SESSION['usuari'] ?? null;
$nombre   = htmlspecialchars($producto['Nombre'],            ENT_QUOTES, 'UTF-8');
$cat      = htmlspecialchars($producto['Categoria'],         ENT_QUOTES, 'UTF-8');
$origen   = htmlspecialchars($producto['ProcedenciaOrigen'], ENT_QUOTES, 'UTF-8');
$formato  = htmlspecialchars($producto['Formato'],           ENT_QUOTES, 'UTF-8');
$desc     = htmlspecialchars($producto['Descripcion'] ?? '', ENT_QUOTES, 'UTF-8');
$precio   = number_format((float)$producto['Precio'], 2, ',', '.');
$intens   = max(0, min(5, (int)$producto['Intensidad']));
$dots     = str_repeat('●', $intens) . str_repeat('○', 5 - $intens);
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
    <title><?= $nombre ?> · KoffTea</title>
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
    <span aria-current="page"><?= $nombre ?></span>
</nav>

    <!-- Detalle producto -->
    <div class="producto-detalle">
        <div class="producto-img-wrap">
            <img src="<?= $img ?>" alt="<?= $nombre ?>">
        </div>

        <div class="producto-info">
            <span class="producto-badge"><?= $cat ?></span>
            <h1 class="producto-nombre"><?= $nombre ?></h1>

            <p class="producto-stars">
                <span id="avg-stars">☆☆☆☆☆</span>
                <span id="avg-count"></span>
            </p>

            <p class="producto-precio"><?= $precio ?> €</p>

            <div class="producto-meta">
                <span><i class="fa-solid fa-location-dot"></i> <?= $origen ?></span>
                <span><i class="fa-solid fa-box"></i> <?= $formato ?></span>
                <span><i class="fa-solid fa-fire-flame-curved"></i>
                    Intensidad: <span class="intensidad-dots"><?= $dots ?></span>
                </span>
                <span>
                    <?php if ($inStock): ?>
                        <i class="fa-solid fa-circle-check" style="color:#4a7c4a"></i>
                        <span class="producto-stock-ok">En stock (<?= (int)$producto['Stock'] ?> uds.)</span>
                    <?php else: ?>
                        <i class="fa-solid fa-circle-xmark" style="color:#c62828"></i>
                        <span class="producto-stock-out">Sin stock</span>
                    <?php endif; ?>
                </span>
            </div>

            <?php if ($desc): ?>
                <p class="producto-desc"><?= $desc ?></p>
            <?php endif; ?>

            <div class="detalle-actions">
                <button class="btn-cart-detalle"
                        <?= $inStock ? '' : 'disabled' ?>
                        onclick="addToCart({id:'<?= htmlspecialchars($producto['ID'], ENT_QUOTES) ?>',nombre:'<?= addslashes($producto['Nombre']) ?>',precio:<?= (float)$producto['Precio'] ?>,categoria:'<?= addslashes($producto['Categoria']) ?>'})"
                        aria-label="<?= $inStock ? "Añadir $nombre al carrito" : "Sin stock" ?>">
                    <?php if ($inStock): ?>
                        <i class="fa-solid fa-cart-plus"></i> Añadir al carrito
                    <?php else: ?>
                        <i class="fa-solid fa-ban"></i> Sin stock
                    <?php endif; ?>
                </button>
                <button class="btn-wishlist-detalle"
                        data-id="<?= htmlspecialchars($producto['ID'], ENT_QUOTES) ?>"
                        onclick="toggleWishlist({id:'<?= htmlspecialchars($producto['ID'], ENT_QUOTES) ?>',nombre:'<?= addslashes($producto['Nombre']) ?>',precio:<?= (float)$producto['Precio'] ?>,categoria:'<?= addslashes($producto['Categoria']) ?>'})"
                        aria-label="Añadir <?= $nombre ?> a lista de deseos">
                    <i class="fa-regular fa-heart"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Comentarios -->
    <section class="comentarios-section">
        <h2><i class="fa-regular fa-comments"></i> Opiniones del producto</h2>

        <!-- Formulario (solo usuarios autenticados) -->
        <?php if ($usuario): ?>
        <div class="comentario-form">
            <h3>Deja tu opinión</h3>
            <div class="star-selector" role="radiogroup" aria-label="Valoración">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>" name="valoracion" value="<?= $i ?>">
                    <label for="star<?= $i ?>" title="<?= $i ?> estrella<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
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

        <div id="comentarios-lista">
            <div class="comentarios-empty">
                <i class="fa-solid fa-spinner fa-spin"></i> Cargando opiniones...
            </div>
        </div>
    </section>

</main>

<footer>
    <p>&copy; 2025 KoffTea Times · Inspirando momentos de lectura y aroma.</p>
    <p>
        <a href="formulario.php">Contacto</a> ·
        <a href="productes.php">Productos</a>
    </p>
</footer>

<!-- ID oculto para JS -->
<input type="hidden" id="producto-id" value="<?= htmlspecialchars($id, ENT_QUOTES) ?>">

<script src="js/cart.js"></script>
<script src="js/wishlist.js"></script>
<script src="js/comments.js"></script>
<script>
function handleSearch(e) {
    e.preventDefault();
    const q = document.getElementById('search-input').value.trim();
    if (q) window.location.href = 'productes.php?q=' + encodeURIComponent(q);
}
</script>
</body>
</html>
