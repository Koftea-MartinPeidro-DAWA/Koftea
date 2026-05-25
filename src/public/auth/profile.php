<?php
session_start();
include '../includes/json_connect.php';

if (isset($_SESSION['usuari'])) {
    $nom_usuari = $_SESSION['usuari'];
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = intval($_COOKIE['user_id']);
} else {
    header("Location: login.php");
    exit();
}

$data    = json_read('../data/users.json');
$usuaris = $data['usuaris'] ?? [];

$usuariActual = null;
foreach ($usuaris as &$usuari) {
    if ((isset($nom_usuari) && $usuari['nom_usuari'] === $nom_usuari) ||
        (isset($user_id)   && $usuari['id']          === $user_id)) {
        $usuariActual = &$usuari;
        break;
    }
}

if (!$usuariActual) {
    header("Location: login.php");
    exit();
}

$missatge = "";
$tipus    = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nou_email   = trim($_POST['email']   ?? '');
    $nou_nom     = trim($_POST['nom']     ?? '');
    $nou_cognoms = trim($_POST['cognoms'] ?? '');
    $nova_pass   = $_POST['nova_contrasenya'] ?? '';

    if (!empty($nou_email) && !filter_var($nou_email, FILTER_VALIDATE_EMAIL)) {
        $missatge = "El formato del email no es válido.";
        $tipus    = "error";
    } else {
        $usuariActual['email']   = $nou_email   ?: $usuariActual['email'];
        $usuariActual['nom']     = $nou_nom     ?: $usuariActual['nom'];
        $usuariActual['cognoms'] = $nou_cognoms ?: $usuariActual['cognoms'];

        if (!empty($nova_pass)) {
            $usuariActual['contrasenya'] = password_hash($nova_pass, PASSWORD_DEFAULT);
        }

        json_write('../data/users.json', $data);
        $missatge = "¡Perfil actualizado correctamente!";
        $tipus    = "success";
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
    <title>Perfil · KoffTea</title>
</head>
<body>

<header class="header">
    <h1><i class="fa-solid fa-mug-hot"></i> KoffTea</h1>
    <a href="../index.php"><i class="fa-solid fa-house"></i> Volver al inicio</a>
</header>

<main>
    <div class="auth-card">
        <div class="avatar"><i class="fa-solid fa-user"></i></div>
        <h2><?= htmlspecialchars($usuariActual['nom_usuari'], ENT_QUOTES, 'UTF-8') ?></h2>

        <?php if ($missatge): ?>
            <div class="msg <?= $tipus ?>">
                <i class="fa-solid <?= $tipus === 'error' ? 'fa-circle-xmark' : 'fa-circle-check' ?>"></i>
                <?= htmlspecialchars($missatge, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="field">
                <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($usuariActual['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="field">
                <label for="nom"><i class="fa-solid fa-id-card"></i> Nombre</label>
                <input type="text" id="nom" name="nom"
                       value="<?= htmlspecialchars($usuariActual['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="field">
                <label for="cognoms"><i class="fa-solid fa-id-card"></i> Apellidos</label>
                <input type="text" id="cognoms" name="cognoms"
                       value="<?= htmlspecialchars($usuariActual['cognoms'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <hr class="divider">

            <div class="field">
                <label for="nova_contrasenya"><i class="fa-solid fa-key"></i> Nueva contraseña</label>
                <input type="password" id="nova_contrasenya" name="nova_contrasenya"
                       placeholder="Déjalo en blanco para no cambiarla">
            </div>

            <button type="submit" class="btn">
                <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
            </button>
        </form>

        <a href="logout.php" class="btn-outline">
            <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
        </a>
    </div>
</main>

<footer>&copy; 2025 KoffTea Times</footer>

</body>
</html>
