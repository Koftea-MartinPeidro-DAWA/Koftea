<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/logo.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/productes.css">
    <script type="importmap">
    {
        "imports": {
            "axios": "https://cdn.jsdelivr.net/npm/axios@1.9.0/+esm"
        }
    }
    </script>
    <title>Productos · KoffTea</title>
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
            <li><a href="productes.php" class="active" aria-current="page">Productos</a></li>
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

<main>
<!-- Breadcrumb -->
<nav class="breadcrumb" aria-label="Ruta de navegación">
    <a href="index.php">Inicio</a>
    <span aria-hidden="true">›</span>
    <span aria-current="page">Productos</span>
</nav>

<div class="page-productes" id="main">

    <!-- Filtros -->
    <aside class="filters" aria-label="Filtros de productos">
        <h2><i class="fa-solid fa-sliders"></i> Filtros</h2>

        <div class="filter-group">
            <h3>Categoría</h3>
            <div id="filter-categories"></div>
        </div>

        <div class="filter-group">
            <h3>Precio</h3>
            <div class="price-inputs">
                <input type="number" id="price-min" placeholder="0" min="0" step="0.5" aria-label="Precio mínimo">
                <span>—</span>
                <input type="number" id="price-max" placeholder="Máx" min="0" step="0.5" aria-label="Precio máximo">
            </div>
        </div>

        <div class="filter-group">
            <h3>Disponibilidad</h3>
            <label class="toggle-label">
                <span class="toggle">
                    <input type="checkbox" id="filter-stock" aria-label="Solo productos con stock">
                    <span class="toggle-slider"></span>
                </span>
                Solo con stock
            </label>
        </div>

        <button class="btn-clear" id="btn-clear-filters">
            <i class="fa-solid fa-rotate-left"></i> Limpiar filtros
        </button>
    </aside>

    <!-- Productos -->
    <section class="products-area" aria-live="polite">
        <h1>Todos los productos</h1>
        <p class="products-count" id="products-count">Cargando...</p>

        <div class="products-grid" id="products-grid">
            <div class="grid-loading">
                <i class="fa-solid fa-spinner fa-spin"></i> Cargando productos...
            </div>
        </div>
    </section>
</div>
</main>

<footer>
    <p>&copy; 2025 KoffTea Times · Inspirando momentos de lectura y aroma.</p>
    <p>
        <a href="formulario.php">Contacto</a> ·
        <a href="cataleg_processor.php">Importar catálogo</a>
    </p>
</footer>

<script src="js/cart.js"></script>
<script src="js/wishlist.js"></script>
<script type="module" src="js/productes.js"></script>
<script>
function handleSearch(e) {
    e.preventDefault();
    const q = document.getElementById('search-input').value.trim();
    if (q) window.location.href = 'productes.php?q=' + encodeURIComponent(q);
}
</script>
</body>
</html>
