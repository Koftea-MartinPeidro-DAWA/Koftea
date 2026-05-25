<?php
session_start();
include '../includes/json_connect.php';

$missatge = "";
$tipus    = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_usuari = trim($_POST['nom_usuari']);
    $contrasenya = $_POST['contrasenya'];
    $email      = trim($_POST['email']);
    $nom        = trim($_POST['nom']);
    $cognoms    = trim($_POST['cognoms']);

    if (empty($nom_usuari) || empty($contrasenya) || empty($email)) {
        $missatge = "Los campos nombre de usuario, contraseña y email son obligatorios.";
        $tipus    = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $missatge = "El formato del email no es válido.";
        $tipus    = "error";
    } else {
        $data    = json_read('../data/users.json');
        $usuaris = $data['usuaris'] ?? [];

        $usuari_existeix = false;
        $email_existeix  = false;
        foreach ($usuaris as $usuari) {
            if ($usuari['nom_usuari'] === $nom_usuari) $usuari_existeix = true;
            if ($usuari['email']      === $email)      $email_existeix  = true;
        }

        if ($usuari_existeix) {
            $missatge = "Este nombre de usuario ya existe.";
            $tipus    = "error";
        } elseif ($email_existeix) {
            $missatge = "Este email ya está registrado.";
            $tipus    = "error";
        } else {
            $id = $usuaris ? (end($usuaris)['id'] + 1) : 1;

            $nou_usuari = [
                "id"            => $id,
                "nom_usuari"    => $nom_usuari,
                "contrasenya"   => password_hash($contrasenya, PASSWORD_DEFAULT),
                "email"         => $email,
                "nom"           => $nom,
                "cognoms"       => $cognoms,
                "data_registre" => gmdate("Y-m-d\TH:i:s\Z")
            ];

            $data['usuaris'][] = $nou_usuari;
            json_write('../data/users.json', $data);

            $missatge = "¡Cuenta creada correctamente! Ahora puedes iniciar sesión.";
            $tipus    = "success";
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
    <title>Registro · KoffTea</title>
</head>
<body>

<header class="header">
    <h1><i class="fa-solid fa-mug-hot"></i> KoffTea</h1>
    <a href="../index.php"><i class="fa-solid fa-house"></i> Volver al inicio</a>
</header>

<main>
    <div class="auth-card">
        <h2><i class="fa-solid fa-user-plus"></i> Crear una cuenta</h2>

        <?php if ($missatge): ?>
            <div class="msg <?= $tipus ?>">
                <i class="fa-solid <?= $tipus === 'error' ? 'fa-circle-xmark' : 'fa-circle-check' ?>"></i>
                <?= htmlspecialchars($missatge, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="field">
                <label for="nom_usuari"><i class="fa-solid fa-user"></i> Nombre de usuario <span style="color:#c62828">*</span></label>
                <input type="text" id="nom_usuari" name="nom_usuari"
                       value="<?= htmlspecialchars($_POST['nom_usuari'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Elige un nombre de usuario" required autofocus>
            </div>
            <div class="field">
                <label for="email"><i class="fa-solid fa-envelope"></i> Email <span style="color:#c62828">*</span></label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="correo@ejemplo.com" required>
            </div>
            <div class="field">
                <label for="contrasenya"><i class="fa-solid fa-lock"></i> Contraseña <span style="color:#c62828">*</span></label>
                <input type="password" id="contrasenya" name="contrasenya"
                       placeholder="Mínimo 6 caracteres" required>
            </div>

            <hr class="divider">

            <div class="field">
                <label for="nom"><i class="fa-solid fa-id-card"></i> Nombre</label>
                <input type="text" id="nom" name="nom"
                       value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Tu nombre">
            </div>
            <div class="field">
                <label for="cognoms"><i class="fa-solid fa-id-card"></i> Apellidos</label>
                <input type="text" id="cognoms" name="cognoms"
                       value="<?= htmlspecialchars($_POST['cognoms'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Tus apellidos">
            </div>

            <button type="submit" class="btn">
                <i class="fa-solid fa-user-plus"></i> Registrarse
            </button>
        </form>

        <div class="auth-footer">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
        </div>
    </div>
</main>

<footer>&copy; 2025 KoffTea Times</footer>

</body>
</html>
