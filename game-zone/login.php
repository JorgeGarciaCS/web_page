<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['username'];
        header("Location: dashboard.php");
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Iniciar Sesión</title></head>
<body>
<h2>Iniciar Sesión</h2>
<form method="POST">
  Usuario: <input type="text" name="username" required><br>
  Contraseña: <input type="password" name="password" required><br>
  <button type="submit">Entrar</button>
</form>
<a href="register.php">¿No tienes cuenta?</a>
</body>
</html>
