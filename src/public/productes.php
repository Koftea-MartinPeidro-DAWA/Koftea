<?php
session_start();

$dataFile = __DIR__ . "/data/productos.json";
$data     = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
$productes = $data['productes'] ?? [];

// Mapa de categoria → imagen
$catImg = [
    'Grano'    => 'images/category/grano.jpg',
    'Molido'   => 'images/category/molido.jpg',
    'Cápsula'  => 'images/category/capsula.jpg',
    'Cápsulas' => 'images/category/capsula.jpg',
    'Té'       => 'images/category/te.jpg',
    'Te'       => 'images/category/te.jpg',
];

function getImg(array $catMap, string $cat): string {
    return $catMap[$cat] ?? 'images/category/grano.jpg';
}

function stars(int $n = 4): string {
    return str_repeat('★', $n) . str_repeat('☆', 5 - $n);
}

$categories = array_values(array_unique(array_column($productes, 'Categoria')));
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
    <title>Productos · KoffTea</title>
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
                <li>
                    <a href="#" aria-label="Lista de deseos">
                        <i class="fa-solid fa-heart" aria-hidden="true"></i>
                    </a>
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
            <?php foreach ($categories as $cat): ?>
                <label>
                    <input type="checkbox" class="filter-cat" value="<?= htmlspecialchars($cat) ?>">
                    <?= htmlspecialchars($cat) ?>
                </label>
            <?php endforeach; ?>
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
        <p class="products-count" id="products-count"><?= count($productes) ?> productos</p>

        <div class="products-grid" id="products-grid">
            <?php foreach ($productes as $p):
                $inStock  = (int)$p['Stock'] > 0;
                $img      = getImg($catImg, $p['Categoria']);
                $nombre   = htmlspecialchars($p['Nombre'],           ENT_QUOTES, 'UTF-8');
                $cat      = htmlspecialchars($p['Categoria'],        ENT_QUOTES, 'UTF-8');
                $origen   = htmlspecialchars($p['ProcedenciaOrigen'],ENT_QUOTES, 'UTF-8');
                $formato  = htmlspecialchars($p['Formato'],          ENT_QUOTES, 'UTF-8');
                $precio   = number_format((float)$p['Precio'], 2, ',', '.');
                $id       = htmlspecialchars($p['ID'],               ENT_QUOTES, 'UTF-8');
            ?>
            <article class="product-card <?= $inStock ? '' : 'out-of-stock' ?>"
                     data-cat="<?= $cat ?>"
                     data-precio="<?= (float)$p['Precio'] ?>"
                     data-stock="<?= (int)$p['Stock'] ?>">
                <div class="card-img">
                    <img src="<?= $img ?>" alt="<?= $nombre ?>" loading="lazy">
                    <span class="badge-categoria"><?= $cat ?></span>
                    <?php if (!$inStock): ?>
                        <span class="badge-stock-out">Sin stock</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <p class="card-nombre"><?= $nombre ?></p>
                    <p class="card-origen"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> <?= $origen ?></p>
                    <p class="card-stars" aria-label="Valoración: 4 de 5 estrellas"><?= stars(4) ?></p>
                    <p class="card-precio"><?= $precio ?> €</p>
                    <p class="card-formato"><?= $formato ?></p>
                </div>
                <button class="btn-cart"
                        data-id="<?= $id ?>"
                        data-nombre="<?= $nombre ?>"
                        data-precio="<?= (float)$p['Precio'] ?>"
                        data-categoria="<?= $cat ?>"
                        <?= $inStock ? '' : 'disabled' ?>
                        onclick="addToCart({id:'<?= $id ?>',nombre:'<?= addslashes($p['Nombre']) ?>',precio:<?= (float)$p['Precio'] ?>,categoria:'<?= addslashes($p['Categoria']) ?>'})"
                        aria-label="<?= $inStock ? "Añadir $nombre al carrito" : "$nombre sin stock" ?>">
                    <?php if ($inStock): ?>
                        <i class="fa-solid fa-cart-plus" aria-hidden="true"></i> Añadir al carrito
                    <?php else: ?>
                        <i class="fa-solid fa-ban" aria-hidden="true"></i> Sin stock
                    <?php endif; ?>
                </button>
            </article>
            <?php endforeach; ?>

            <div class="empty-state" id="empty-state" hidden>
                <i class="fa-solid fa-magnifying-glass"></i>
                <p>Ningún producto coincide con los filtros seleccionados.</p>
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
<script>
// ── Filtrado ──
const cards      = Array.from(document.querySelectorAll('.product-card'));
const emptyState = document.getElementById('empty-state');
const countEl    = document.getElementById('products-count');

function applyFilters() {
    const cats      = [...document.querySelectorAll('.filter-cat:checked')].map(c => c.value);
    const minPrice  = parseFloat(document.getElementById('price-min').value) || 0;
    const maxPrice  = parseFloat(document.getElementById('price-max').value) || Infinity;
    const onlyStock = document.getElementById('filter-stock').checked;
    const query     = document.getElementById('search-input')?.value.toLowerCase() || '';

    let visible = 0;
    cards.forEach(card => {
        const cat    = card.dataset.cat;
        const precio = parseFloat(card.dataset.precio);
        const stock  = parseInt(card.dataset.stock);
        const nombre = card.querySelector('.card-nombre').textContent.toLowerCase();

        const okCat   = cats.length === 0 || cats.includes(cat);
        const okPrice = precio >= minPrice && precio <= maxPrice;
        const okStock = !onlyStock || stock > 0;
        const okQuery = !query || nombre.includes(query);

        const show = okCat && okPrice && okStock && okQuery;
        card.hidden = !show;
        if (show) visible++;
    });

    emptyState.hidden = visible > 0;
    countEl.textContent = `${visible} productos`;
}

document.querySelectorAll('.filter-cat, #filter-stock').forEach(el =>
    el.addEventListener('change', applyFilters)
);
document.getElementById('price-min').addEventListener('input', applyFilters);
document.getElementById('price-max').addEventListener('input', applyFilters);
document.getElementById('search-input')?.addEventListener('input', applyFilters);

document.getElementById('btn-clear-filters').addEventListener('click', () => {
    document.querySelectorAll('.filter-cat').forEach(c => c.checked = false);
    document.getElementById('price-min').value = '';
    document.getElementById('price-max').value = '';
    document.getElementById('filter-stock').checked = false;
    if (document.getElementById('search-input')) document.getElementById('search-input').value = '';
    applyFilters();
});

function handleSearch(e) {
    e.preventDefault();
    applyFilters();
}

// ── Inicializar desde parámetros URL ──
(function() {
    const p = new URLSearchParams(window.location.search);
    const cat = p.get('cat');
    const q   = p.get('q');
    if (cat) {
        document.querySelectorAll('.filter-cat').forEach(cb => {
            if (cb.value === cat) cb.checked = true;
        });
    }
    if (q) {
        const si = document.getElementById('search-input');
        if (si) si.value = q;
    }
    if (cat || q) applyFilters();
})();
</script>
</body>
</html>
