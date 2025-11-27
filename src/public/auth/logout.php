<?php
session_start();

// Eliminar la cookie d’identificació
setcookie('user_id', '', time() - 3600, "/");

// Destruir la sessió
$_SESSION = [];
session_destroy();

// Redirigir a la pàgina de login
header("Location: ../index.php");
exit();
?>
