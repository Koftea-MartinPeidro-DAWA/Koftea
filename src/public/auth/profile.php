<?php
session_start();
include '../includes/json_connect.php';

// 🔎 Identificar usuari: sessió o cookie
if (isset($_SESSION['usuari'])) {
    $nom_usuari = $_SESSION['usuari'];
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = intval($_COOKIE['user_id']);
} else {
    header("Location: login.php");
    exit();
}

// Llegeix JSON
$data = json_read('../data/users.json');
$usuaris = $data['usuaris'] ?? [];

$usuariActual = null;

// GET /usuaris/{id} simulada
foreach ($usuaris as &$usuari) {
    if ((isset($nom_usuari) && $usuari['nom_usuari'] === $nom_usuari) ||
        (isset($user_id) && $usuari['id'] === $user_id)) {
        $usuariActual = &$usuari;
        break;
    }
}

if (!$usuariActual) {
    die("Usuari no trobat.");
}

$missatge = "";

// PATCH /usuaris/{id} simulada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuariActual['email'] = $_POST['email'] ?? $usuariActual['email'];
    $usuariActual['nom'] = $_POST['nom'] ?? $usuariActual['nom'];
    $usuariActual['cognoms'] = $_POST['cognoms'] ?? $usuariActual['cognoms'];

    if (!empty($_POST['nova_contrasenya'])) {
        $usuariActual['contrasenya'] = password_hash($_POST['nova_contrasenya'], PASSWORD_DEFAULT);
    }

    // Guarda al JSON
    json_write('../data/users.json', $data);
    $missatge = "Perfil actualitzat correctament!";
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/profile.css">
    <title>Perfil d'usuari</title>
</head>
<body>

    <h2>Perfil de l'usuari: <?= htmlspecialchars($usuariActual['nom_usuari']) ?></h2>

    <?php if($missatge) echo "<p>$missatge</p>"; ?>

    <form method="post">
        Email: <input type="email" name="email" value="<?= htmlspecialchars($usuariActual['email']) ?>"><br>
        Nom: <input type="text" name="nom" value="<?= htmlspecialchars($usuariActual['nom']) ?>"><br>
        Cognoms: <input type="text" name="cognoms" value="<?= htmlspecialchars($usuariActual['cognoms']) ?>"><br>
        Nova contrasenya: <input type="password" name="nova_contrasenya"><br>
        <button type="submit">Actualitzar perfil</button>
    </form>

    <p><a href="logout.php">Tancar sessió</a></p>
</body>
</html>
