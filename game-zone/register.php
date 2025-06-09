<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $password]);
        header("Location: login.php");
    } catch (PDOException $e) {
        echo "Error al registrar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Registro</title></head>
<body>
<h2>Registro de Usuario</h2>
<form method="POST">
  Usuario: <input type="text" name="username" required><br>
  Correo: <input type="email" name="email" required><br>
  Contraseña: <input type="password" name="password" required><br>
  <button type="submit">Registrarse</button>
</form>
<a href="login.php">¿Ya tienes cuenta?</a>
</body>
</html>
