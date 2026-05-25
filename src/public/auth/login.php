<?php
session_start();
include '../includes/json_connect.php';

$missatge = "";
$tipus    = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_usuari = trim($_POST['nom_usuari']);
    $contrasenya = $_POST['contrasenya'];

    if (empty($nom_usuari) || empty($contrasenya)) {
        $missatge = "Rellena todos los campos.";
        $tipus    = "error";
    } else {
        $data    = json_read('../data/users.json');
        $usuaris = $data['usuaris'] ?? [];

        $usuariTrobat = null;
        foreach ($usuaris as $usuari) {
            if ($usuari['nom_usuari'] === $nom_usuari) {
                $usuariTrobat = $usuari;
                break;
            }
        }

        if ($usuariTrobat && password_verify($contrasenya, $usuariTrobat['contrasenya'])) {
            session_regenerate_id(true);
            $_SESSION['usuari'] = $usuariTrobat['nom_usuari'];
            setcookie('user_id', $usuariTrobat['id'], time() + 3600, "/");
            header("Location: profile.php");
            exit();
        } else {
            $missatge = "Nombre de usuario o contraseña incorrectos.";
            $tipus    = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/auth.css">
    <title>Inicio de sesión · KoffTea</title>
</head>
<body>

<header class="header">
    <h1><i class="fa-solid fa-mug-hot"></i> KoffTea</h1>
    <a href="../index.php"><i class="fa-solid fa-house"></i> Volver al inicio</a>
</header>

<main>
    <div class="auth-card">
        <h2><i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión</h2>

        <?php if ($missatge): ?>
            <div class="msg <?= $tipus ?>">
                <i class="fa-solid <?= $tipus === 'error' ? 'fa-circle-xmark' : 'fa-circle-check' ?>"></i>
                <?= htmlspecialchars($missatge, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="field">
                <label for="nom_usuari"><i class="fa-solid fa-user"></i> Nombre de usuario</label>
                <input type="text" id="nom_usuari" name="nom_usuari"
                       value="<?= htmlspecialchars($_POST['nom_usuari'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Tu nombre de usuario" required autofocus>
            </div>
            <div class="field">
                <label for="contrasenya"><i class="fa-solid fa-lock"></i> Contraseña</label>
                <input type="password" id="contrasenya" name="contrasenya"
                       placeholder="Tu contraseña" required>
            </div>
            <button type="submit" class="btn">
                <i class="fa-solid fa-right-to-bracket"></i> Entrar
            </button>
        </form>

        <div class="auth-footer">
            ¿No tienes cuenta? <a href="register.php">Regístrate</a>
        </div>
    </div>
</main>

<footer>&copy; 2025 KoffTea Times</footer>

</body>
</html>
