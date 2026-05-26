<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/logo.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/productes.css">
    <title>KoffTea Times</title>
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
            <li><a href="index.php" class="active" aria-current="page">Inicio</a></li>
            <li><a href="productes.php">Productos</a></li>
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
        <a href="productes.php" class="btn">Ver productos</a>
    </div>
</div>

<main id="main" class="home-main">
    <section class="hero">
        <div class="hero-text">
            <h1>El arte de disfrutar el café y el té con calma</h1>
            <p class="quote">
                "Entre sorbos y páginas, el mundo se detiene por un instante."
            </p>
            <a href="productes.php" class="hero-btn">Ver productos</a>
        </div>
        <div class="hero-img">
            <img src="./images/products/arab_coffee.png" alt="Paquete de café arábica">
        </div>
    </section>

    <section class="features">
        <h2>Secciones destacadas</h2>
        <div class="feature-items">
            <article>
                <a href="productes.php?cat=Cápsulas">
                    <img src="./images/category/capsula.jpg" alt="Cápsulas de café">
                    <h3>Cápsulas</h3>
                    <p>Comodidad moderna para los amantes del espresso perfecto.</p>
                </a>
            </article>
            <article>
                <a href="productes.php?cat=Grano">
                    <img src="./images/category/grano.jpg" alt="Granos de café">
                    <h3>Grano</h3>
                    <p>El aroma y la frescura en su forma más pura.</p>
                </a>
            </article>
            <article>
                <a href="productes.php?cat=Molido">
                    <img src="./images/category/molido.jpg" alt="Café molido">
                    <h3>Molido</h3>
                    <p>La sencillez de un café rápido sin perder el placer.</p>
                </a>
            </article>
            <article>
                <a href="productes.php?cat=Té">
                    <img src="./images/category/te.jpg" alt="Té">
                    <h3>Té</h3>
                    <p>Variedades que invitan a la calma y la reflexión.</p>
                </a>
            </article>
        </div>
    </section>

    <section class="video-section">
        <h2>Vídeo del Día: La Taza Perfecta</h2>
        <div class="video-container">
            <video controls autoplay muted loop aria-label="Vídeo: cómo preparar la taza perfecta">
                <source src="./images/videoDia.mp4" type="video/mp4">
            </video>
        </div>
        <p class="video-caption" id="footer-video">
            Aprende la técnica definitiva para preparar tu bebida matutina, ya sea café de prensa francesa o una infusión de té verde.
        </p>
    </section>
</main>

<footer>
    <p>&copy; 2025 KoffTea Times · Inspirando momentos de lectura y aroma.</p>
    <p>
        <a href="formulario.php">Contacto</a> ·
        <a href="productes.php">Productos</a> ·
        <a href="cataleg_processor.php">Importar catálogo</a>
    </p>
</footer>

<script src="js/cart.js"></script>
<script src="js/wishlist.js"></script>
<script>
function handleSearch(e) {
    e.preventDefault();
    const q = document.getElementById('search-input').value.trim();
    if (q) window.location.href = 'productes.php?q=' + encodeURIComponent(q);
}
</script>
</body>
</html>
