<?php
require 'config.php';

$stmt = $pdo->query("SELECT id FROM recipes ORDER BY RAND() LIMIT 1");
$recipe = $stmt->fetch();

if ($recipe) {
    header("Location: view.php?id=" . $recipe['id']);
} else {
    header("Location: index.php");
}
exit;