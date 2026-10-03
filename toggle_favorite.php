<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $stmt = $pdo->prepare("UPDATE recipes SET is_favorite = NOT is_favorite WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;