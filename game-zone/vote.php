<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user']) || !isset($_GET['post_id'])) {
    header("Location: login.php");
    exit;
}

$post_id = (int) $_GET['post_id'];

$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$_SESSION['user']]);
$user_id = $stmt->fetchColumn();

// Evitar votos duplicados
$check = $pdo->prepare("SELECT id FROM votes WHERE user_id = ? AND post_id = ?");
$check->execute([$user_id, $post_id]);

if (!$check->fetch()) {
    $insert = $pdo->prepare("INSERT INTO votes (user_id, post_id) VALUES (?, ?)");
    $insert->execute([$user_id, $post_id]);
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
