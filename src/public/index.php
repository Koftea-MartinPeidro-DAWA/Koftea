<!DOCTYPE html>
<html lang="ca">
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

<a class="skip-link" href="#main">Saltar al contingut</a>

<header class="header">
    <div class="logo">
        <a href="index.php" aria-label="KoffTea - Inici">
            <img src="images/logo.png" alt="KoffTea">
        </a>
    </div>

    <nav class="main-nav" id="main-nav" aria-label="Navegació principal">
        <ul>
            <li><a href="index.php" class="active" aria-current="page">Inici</a></li>
            <li><a href="productes.php">Productes</a></li>
            <li><a href="formulario.php">Contacte</a></li>
        </ul>
    </nav>

    <div class="header-right">
        <form class="search-bar" role="search" onsubmit="handleSearch(event)">
            <label for="search-input" class="visually-hidden">Cerca productes</label>
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <input type="search" id="search-input" name="q" placeholder="Buscar cafés o tés...">
        </form>

        <nav class="nav-icons" aria-label="Accions d'usuari">
            <ul>
                <li>
                    <a href="auth/profile.php" aria-label="Perfil d'usuari">
                        <i class="fa-solid fa-user" aria-hidden="true"></i>
                    </a>
                </li>
                <li>
                    <a href="#" aria-label="Llista de desitjos">
                        <i class="fa-solid fa-heart" aria-hidden="true"></i>
                    </a>
                </li>
                <li class="cart-wrapper">
                    <button class="cart-btn" aria-label="Carret de compra" aria-expanded="false">
                        <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
                        <span class="cart-badge" id="cart-badge" hidden>0</span>
                    </button>
                </li>
            </ul>
        </nav>

        <button class="hamburger" aria-label="Obrir menú" aria-expanded="false" aria-controls="main-nav">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- Mini-cart -->
<div class="mini-cart" id="mini-cart" role="dialog" aria-label="Carret de compra" hidden>
    <div class="mini-cart-header"><i class="fa-solid fa-cart-shopping"></i> El teu carret</div>
    <div class="mini-cart-items" id="mini-cart-items"></div>
    <div class="mini-cart-footer">
        <span>Total: <strong id="mini-cart-total">0,00 €</strong></span>
        <a href="productes.php" class="btn">Veure productes</a>
    </div>
</div>

<main id="main">
    <section class="hero">
        <div class="hero-text">
            <h1>L'art de gaudir el cafè i el té amb calma</h1>
            <p class="quote">
                "Entre glops i pàgines, el món s'atura per un instant."
            </p>
            <a href="productes.php" class="hero-btn">Veure productes</a>
        </div>
        <div class="hero-img">
            <img src="./images/products/arab_coffee.png" alt="Paquet de cafè arabiga">
        </div>
    </section>

    <section class="features">
        <h2>Seccions destacades</h2>
        <div class="feature-items">
            <article>
                <a href="productes.php?cat=Cápsula">
                    <img src="./images/category/capsula.jpg" alt="Càpsules de cafè">
                    <h3>Càpsules</h3>
                    <p>Comoditat moderna per als amants del espresso perfecte.</p>
                </a>
            </article>
            <article>
                <a href="productes.php?cat=Grano">
                    <img src="./images/category/grano.jpg" alt="Grans de cafè">
                    <h3>Gra</h3>
                    <p>L'aroma i la frescor en la seua forma més pura.</p>
                </a>
            </article>
            <article>
                <a href="productes.php?cat=Molido">
                    <img src="./images/category/molido.jpg" alt="Cafè mòlt">
                    <h3>Mòlt</h3>
                    <p>La senzillesa d'un cafè ràpid sense perdre el plaer.</p>
                </a>
            </article>
            <article>
                <a href="productes.php?cat=Té">
                    <img src="./images/category/te.jpg" alt="Té">
                    <h3>Té</h3>
                    <p>Varietats que inviten a la calma i la reflexió.</p>
                </a>
            </article>
        </div>
    </section>

    <section class="video-section">
        <h2>Vídeo del Dia: La Tassa Perfecta</h2>
        <div class="video-container">
            <video controls autoplay muted loop aria-label="Vídeo: com preparar la tassa perfecta">
                <source src="./images/videoDia.mp4" type="video/mp4">
            </video>
        </div>
        <p class="video-caption" id="footer-video">
            Aprèn la tècnica definitiva per preparar la teua beguda matutina, ja siga cafè de premsa francesa o una infusió de té verd.
        </p>
    </section>
</main>

<footer>
    <p>&copy; 2025 KoffTea Times · Inspirant moments de lectura i aroma.</p>
    <p>
        <a href="formulario.php">Contacte</a> ·
        <a href="productes.php">Productes</a> ·
        <a href="cataleg_processor.php">Importar catàleg</a>
    </p>
</footer>

<script src="js/cart.js"></script>
<script>
function handleSearch(e) {
    e.preventDefault();
    const q = document.getElementById('search-input').value.trim();
    if (q) window.location.href = 'productes.php?q=' + encodeURIComponent(q);
}
</script>
</body>
</html>
