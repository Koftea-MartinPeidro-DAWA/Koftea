<?php
// Inicializar variables
$nombre = $email = $mensaje = "";
$errores = [];
$exito = "";

// Comprobar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ✅ Solo validar en PHP si el checkbox no está marcado
    if (!isset($_POST["validar-js"])) {

        // 1️⃣ Validación de campos
        if (empty($_POST["nombre"])) {
            $errores[] = "El nombre es obligatorio.";
        } else {
            $nombre = htmlspecialchars(trim($_POST["nombre"]));
        }

        if (empty($_POST["email"])) {
            $errores[] = "El correo electrónico es obligatorio.";
        } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo electrónico no es válido.";
        } else {
            $email = htmlspecialchars(trim($_POST["email"]));
        }

        if (empty($_POST["mensaje"])) {
            $errores[] = "El mensaje es obligatorio.";
        } else {
            $mensaje = htmlspecialchars(trim($_POST["mensaje"]));
        }

        if (!isset($_POST["acepto"])) {
            $errores[] = "Debes aceptar los términos y condiciones.";
        }

        // 2️⃣ Validar archivo adjunto (opcional)
        if (isset($_FILES["upload"]) && $_FILES["upload"]["error"] === 0) {
            $archivo = $_FILES["upload"];
            $ext_permitidas = ["jpg", "jpeg", "png", "pdf"];
            $ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));

            if (!in_array($ext, $ext_permitidas)) {
                $errores[] = "Tipo de archivo no permitido. Solo JPG, PNG o PDF.";
            }
        }
    }

    // 3️⃣ Si no hay errores o la validación se hizo en JS
    if (empty($errores) || isset($_POST["validar-js"])) {
        // Solo mover archivo si se subió uno
        if (isset($archivo)) {
            move_uploaded_file($archivo["tmp_name"], "uploads/" . basename($archivo["name"]));
        }

        $exito = "Formulario enviado correctamente. ¡Gracias, $nombre!";
        // Limpiar campos
        $nombre = $email = $mensaje = "";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Contacto Kofftea</title>
    <link rel="stylesheet" href="./src/style.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="form-page">
    <div class="container">

        <form action="" method="POST" enctype="multipart/form-data">
            <h2>Formulario de Contacto Kofftea</h2>

            <!-- Mostrar errores -->
            <?php if (!empty($errores)) : ?>
                <div id="mensaje-estado" style="color: red; text-align: left; margin-bottom: 1rem;">
                    <ul>
                        <?php foreach ($errores as $error) : ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Mensaje de éxito -->
            <?php if ($exito) : ?>
                <div id="mensaje-estado" style="color: green; text-align: center; margin-bottom: 1rem;">
                    <?= $exito ?>
                </div>
            <?php endif; ?>

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?= $nombre ?>">

            <label for="email">Correo electrónico:</label>
            <input type="text" id="email" name="email" value="<?= $email ?>">

            <label for="mensaje">Mensaje:</label>
            <textarea id="mensaje" name="mensaje" rows="4"><?= $mensaje ?></textarea>

            <label for="upload">Adjuntar archivo:</label>
            <input type="file" id="upload" name="upload">

            <div class="checkbox-group">
                <input type="checkbox" id="acepto" name="acepto" <?= isset($_POST["acepto"]) ? "checked" : "" ?>> 
                <label id="acepto-label" for="acepto">Acepto los <a href="#">términos y condiciones</a> y la <a href="#">política de privacidad</a>.</label>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="publicidad" name="publicidad" <?= isset($_POST["publicidad"]) ? "checked" : "" ?>>
                <label for="publicidad">Deseo recibir novedades y promociones de Kofftea por correo electrónico.</label>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="validar-js" name="validar-js" <?= isset($_POST["validar-js"]) ? "checked" : "" ?>>
                <label for="validar-js">Validación con JavaScript. Por defecto valida en php.</label>
            </div>

            <button type="submit">Enviar</button>
        </form>
    </div>
    <script src="./src/form.js"></script>
</body>
</html>
