<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->execute([$id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recipe['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-danger mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-journal-richtext"></i> My Recipe Book</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="h2"><?= htmlspecialchars($recipe['title']) ?></h1>
                            <p class="text-muted">
                                <span class="badge bg-secondary"><?= htmlspecialchars($recipe['category']) ?></span>
                                <?php if ($recipe['prep_time']): ?>
                                    · <i class="bi bi-clock"></i> <?= htmlspecialchars($recipe['prep_time']) ?>
                                <?php endif; ?>
                                <?php if ($recipe['servings']): ?>
                                    · <i class="bi bi-people"></i> <?= htmlspecialchars($recipe['servings']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <a href="edit.php?id=<?= $recipe['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="delete.php?id=<?= $recipe['id'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete this recipe?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>

                    <h5>Ingredients</h5>
                    <div class="bg-light p-3 rounded mb-4" style="white-space: pre-line;">
                        <?= nl2br(htmlspecialchars($recipe['ingredients'])) ?>
                    </div>

                    <h5>Instructions</h5>
                    <div class="bg-light p-3 rounded" style="white-space: pre-line;">
                        <?= nl2br(htmlspecialchars($recipe['instructions'])) ?>
                    </div>

                    <div class="mt-4">
                        <a href="index.php" class="btn btn-outline-danger">
                            <i class="bi bi-arrow-left"></i> Back to recipes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>