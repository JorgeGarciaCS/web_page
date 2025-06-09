<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Panel</title></head>
<body>
<h2>Bienvenido, <?= $_SESSION['user'] ?> 🎮</h2>
<p>¡Ya puedes compartir tus mods y guías!</p>
<a href="logout.php">Cerrar sesión</a>
</body>
</html>
