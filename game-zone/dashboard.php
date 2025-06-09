<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Obtener categorías
$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$cat_filter = isset($_GET['cat']) ? (int)$_GET['cat'] : null;

// Obtener publicaciones filtradas
if ($cat_filter) {
    $stmt = $pdo->prepare("SELECT posts.id, title, content, users.username, posts.created_at, categories.name AS category
                           FROM posts 
                           JOIN users ON posts.user_id = users.id 
                           JOIN categories ON posts.category_id = categories.id
                           WHERE posts.category_id = ?
                           ORDER BY posts.created_at DESC");
    $stmt->execute([$cat_filter]);
} else {
    $stmt = $pdo->query("SELECT posts.id, title, content, users.username, posts.created_at, categories.name AS category
                         FROM posts 
                         JOIN users ON posts.user_id = users.id 
                         JOIN categories ON posts.category_id = categories.id
                         ORDER BY posts.created_at DESC");
}
$posts = $stmt->fetchAll();

// Preparar consulta para contar votos
$vote_stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE post_id = ?");
?>

<!DOCTYPE html>
<html>
<head><title>Zona Gamer - Dashboard</title></head>
<body>
<h2>Bienvenido, <?= $_SESSION['user'] ?> 🎮</h2>
<p><a href="create_post.php">📘 Publicar guía o mod</a> | <a href="logout.php">Cerrar sesión</a></p>

<h3>Filtrar por juego:</h3>
<ul>
  <li><a href="dashboard.php">Todos</a></li>
  <?php foreach ($cats as $cat): ?>
    <li><a href="dashboard.php?cat=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
  <?php endforeach; ?>
</ul>

<h3>Últimos contenidos:</h3>

<?php foreach ($posts as $post): ?>
  <?php
    $vote_stmt->execute([$post['id']]);
    $votos = $vote_stmt->fetchColumn();
  ?>
  <div style="border:1px solid #333; padding:10px; margin-bottom:10px;">
    <h4><?= htmlspecialchars($post['title']) ?></h4>
    <p><?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?>...</p>
    <small>
      Por <?= htmlspecialchars($post['username']) ?> - <?= $post['created_at'] ?> |
      Categoría: <strong><?= htmlspecialchars($post['category']) ?></strong>
    </small><br>
    <strong>👍 <?= $votos ?></strong>
    <?php if (isset($_SESSION['user'])): ?>
      | <a href="vote.php?post_id=<?= $post['id'] ?>">Votar</a>
    <?php endif; ?>
    <br>
    <a href="view_post.php?id=<?= $post['id'] ?>">Leer más</a>
  </div>
<?php endforeach; ?>
</body>
</html>
