<?php
session_start();
include '../includes/json_connect.php'; // Funcions per llegir/escriure JSON

$missatge = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_usuari = trim($_POST['nom_usuari']);
    $contrasenya = $_POST['contrasenya'];

    if (empty($nom_usuari) || empty($contrasenya)) {
        $missatge = "Omple tots els camps.";
    } else {
        $data = json_read('../data/users.json');
        $usuaris = $data['usuaris'] ?? [];

        // Cerca de l'usuari pel nom d'usuari (GET /usuaris?nom_usuari=...)
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
        }
        else {
            $missatge = "Nom d'usuari o contrasenya incorrectes.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/login.css">
    <title>Inici de sessió</title>
</head>
<body>
    <h1>Inicia sessió</h1>
    <br>
    <?php if($missatge) echo "<p>$missatge</p>"; ?>
    <form method="post">
        Nom d'usuari: <input type="text" name="nom_usuari" required><br>
        Contrasenya: <input type="password" name="contrasenya" required><br>
        <button type="submit">Entrar</button>
    </form>
    <br>
    <p>No tens compte? <a href="register.php">Registra’t</a></p>
</body>
</html>
