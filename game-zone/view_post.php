<?php
require 'db.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Publicación inválida.");
}

$post_id = (int) $_GET['id'];

// Obtener post
$stmt = $pdo->prepare("SELECT posts.title, posts.content, posts.created_at, users.username
                       FROM posts JOIN users ON posts.user_id = users.id
                       WHERE posts.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    die("Publicación no encontrada.");
}

// Obtener comentarios
$comments_stmt = $pdo->prepare("SELECT comments.comment, comments.created_at, users.username 
                                FROM comments JOIN users ON comments.user_id = users.id 
                                WHERE post_id = ? ORDER BY comments.created_at DESC");
$comments_stmt->execute([$post_id]);
$comments = $comments_stmt->fetchAll();

// Manejo de nuevo comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
    $comment = trim($_POST['comment']);
    if ($comment !== "") {
        // Obtener ID de usuario
        $user_stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $user_stmt->execute([$_SESSION['user']]);
        $user_id = $user_stmt->fetchColumn();

        $insert = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
        $insert->execute([$post_id, $user_id, htmlspecialchars($comment)]);
        header("Location: view_post.php?id=$post_id"); // Evitar reenvío POST
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head><title><?= htmlspecialchars($post['title']) ?></title></head>
<body>
<h2><?= htmlspecialchars($post['title']) ?></h2>
<small>Por <?= htmlspecialchars($post['username']) ?> - <?= $post['created_at'] ?></small>
<p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
<a href="dashboard.php">⬅ Volver</a>

<hr>

<h3>Comentarios</h3>
<?php if (isset($_SESSION['user'])): ?>
  <form method="POST">
    <textarea name="comment" rows="4" cols="60" placeholder="Escribe un comentario..." required></textarea><br>
    <button type="submit">Comentar</button>
  </form>
<?php else: ?>
  <p><a href="login.php">Inicia sesión</a> para comentar.</p>
<?php endif; ?>

<?php if ($comments): ?>
  <?php foreach ($comments as $c): ?>
    <div style="border: 1px solid #333; padding: 8px; margin: 10px 0;">
      <strong><?= htmlspecialchars($c['username']) ?></strong> <em><?= $c['created_at'] ?></em>
      <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <p>No hay comentarios aún.</p>
<?php endif; ?>
</body>
</html>
