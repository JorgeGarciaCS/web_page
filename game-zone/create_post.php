<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $category_id = (int) $_POST['category_id'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$_SESSION['user']]);
    $user = $stmt->fetch();

    $insert = $pdo->prepare("INSERT INTO posts (user_id, title, content, category_id) VALUES (?, ?, ?, ?)");
    $insert->execute([$user['id'], $title, $content, $category_id]);

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Publicar Contenido</title></head>
<body>
<h2>Publicar nueva guía o mod</h2>
<form method="POST">
  Título: <input type="text" name="title" required><br><br>
  Categoría:
  <select name="category_id" required>
    <?php foreach ($categories as $cat): ?>
      <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
    <?php endforeach; ?>
  </select><br><br>
  Contenido:<br>
  <textarea name="content" rows="10" cols="60" required></textarea><br><br>
  <button type="submit">Publicar</button>
</form>
<a href="dashboard.php">Volver al panel</a>
</body>
</html>
