<?php
session_start();
include '../includes/json_connect.php'; // Funcions per llegir/escriure JSON

$missatge = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_usuari = trim($_POST['nom_usuari']);
    $contrasenya = $_POST['contrasenya'];
    $email = trim($_POST['email']);
    $nom = trim($_POST['nom']);
    $cognoms = trim($_POST['cognoms']);

    // Comprova que no hi hagi camps buits
    if (empty($nom_usuari) || empty($contrasenya) || empty($email)) {
        $missatge = "Els camps nom d'usuari, contrasenya i email són obligatoris.";
    } else {
        // Hasheja la contrasenya
        $hash = password_hash($contrasenya, PASSWORD_DEFAULT);

        // Llegeix l’array existent d’usuaris
        $data = json_read('../data/users.json');
        $usuaris = $data['usuaris'] ?? [];

        // Comprova si el nom d’usuari ja existeix
        $existeix = false;
        foreach ($usuaris as $usuari) {
            if ($usuari['nom_usuari'] === $nom_usuari) {
                $existeix = true;
                break;
            }
        }

        if ($existeix) {
            $missatge = "Aquest nom d'usuari ja existeix.";
        } else {
            // Assigna un ID automàtic
            $id = $usuaris ? end($usuaris)['id'] + 1 : 1;

            // Crea el nou usuari
            $nou_usuari = [
                "id" => $id,
                "nom_usuari" => $nom_usuari,
                "contrasenya" => $hash,
                "email" => $email,
                "nom" => $nom,
                "cognoms" => $cognoms,
                "data_registre" => gmdate("Y-m-d\TH:i:s\Z")
            ];

            // Afegim l’usuari i guardem
            $data['usuaris'][] = $nou_usuari;
            json_write('../data/users.json', $data);

            $missatge = "Usuari registrat correctament!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/register.css">
    <title>Registre d'usuari</title>
</head>
<body>
    <h2>Registre</h2>
    <?php if($missatge) echo "<p>$missatge</p>"; ?>
    <form method="post">
        Nom d'usuari: <input type="text" name="nom_usuari" required><br>
        Contrasenya: <input type="password" name="contrasenya" required><br>
        Email: <input type="email" name="email" required><br>
        Nom: <input type="text" name="nom"><br>
        Cognoms: <input type="text" name="cognoms"><br>
        <button type="submit">Registrar-se</button>
    </form>
    <p>Ja tens compte? <a href="login.php">Inicia sessió</a></p>
</body>
</html>
