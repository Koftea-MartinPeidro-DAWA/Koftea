<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/logo.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
    <title>KoffTea Times</title>
</head>
<header class="header">
        <div class="logo">
            <a href="index.html">
                <img src="images/logo.png" alt="Logo de KoffTea">
            </a>
        </div>
        <div class="search-bar">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Buscar artículos, cafés o tés...">
        </div>
        <nav class="nav-icons">
            <ul>
                <li><a href="auth/profile.php"><i class="fa-solid fa-user"></i></a></li>
                <li><a href="#"><i class="fa-solid fa-heart"></i></a></li>
                <li><a href="#"><i class="fa-solid fa-cart-shopping"></i></a></li>
            </ul>
        </nav>
    </header>
<body>

    <main>
        <section class="hero">
            <div class="hero-text">
                <h1>El arte de disfrutar el café y el té con calma</h1>
                <p class="quote">
                    “Entre sorbos y páginas, el mundo se detiene por un instante.”
                </p>
                <button>Leer más</button>
            </div>
            <div class="hero-img">
                <img src="./images/products/arab_coffee.png" alt="Paquete de café">
            </div>
        </section>

        <section class="features">
            <h2>Secciones destacadas</h2>
            <div class="feature-items">
                <article>
                    <img src="./images/category/capsula.jpg" alt="Cápsulas de café">
                    <h3>Cápsulas</h3>
                    <p>Comodidad moderna para los amantes del espresso perfecto.</p>
                </article>
                <article>
                    <img src="./images/category/grano.jpg" alt="Granos de café">
                    <h3>Grano</h3>
                    <p>El aroma y la frescura en su forma más pura.</p>
                </article>
                <article>
                    <img src="./images/category/molido.jpg" alt="Café soluble">
                    <h3>Soluble</h3>
                    <p>La simplicidad de un café rápido sin perder el placer.</p>
                </article>
                <article>
                    <img src="./images/category/te.jpg" alt="Té">
                    <h3>Té</h3>
                    <p>Variedades que invitan a la calma y la reflexión.</p>
                </article>
            </div>
        </section>

        <section class="video-section">
            <h2>Video del Día: La Taza Perfecta</h2>
            <div class="video-container">
                <video width="560" height="315" controls autoplay muted loop>
                    <source src="./images/videoDia.mp4" type="video/mp4">
                </video>
            </div>
            <p class="video-caption" id="footer-video">Aprende la técnica definitiva para preparar tu bebida matutina, ya sea café de prensa francesa o una infusión de té verde.</p>
        </section>
        </main>
</body>
    <footer>
        <p>&copy; 2025 KoffTea Times · Inspirando momentos de lectura y aroma.</p>
        <p>
            <a href="formulario.php">Formulario de contacto</a>
            <a href="cataleg_processor.php">Formulario de subida</a>
        </p>
    </footer>
</html>
