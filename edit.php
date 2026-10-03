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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'Other');
    $prep_time = trim($_POST['prep_time'] ?? '');
    $servings = trim($_POST['servings'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');

    if ($title && $ingredients && $instructions) {
        $stmt = $pdo->prepare("UPDATE recipes SET title=?, category=?, prep_time=?, servings=?, ingredients=?, instructions=? WHERE id=?");
        $stmt->execute([$title, $category, $prep_time, $servings, $ingredients, $instructions, $id]);
        header("Location: view.php?id=$id");
        exit;
    } else {
        $error = 'Please fill in Title, Ingredients, and Instructions.';
    }
} else {
    // Prefill form
    $_POST = $recipe;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Recipe</title>
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
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Edit Recipe</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Recipe Title *</label>
                            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($_POST['title']) ?>">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($_POST['category']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Prep Time</label>
                                <input type="text" name="prep_time" class="form-control" value="<?= htmlspecialchars($_POST['prep_time']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Servings</label>
                                <input type="text" name="servings" class="form-control" value="<?= htmlspecialchars($_POST['servings']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ingredients *</label>
                            <textarea name="ingredients" class="form-control" rows="6" required><?= htmlspecialchars($_POST['ingredients']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Instructions *</label>
                            <textarea name="instructions" class="form-control" rows="8" required><?= htmlspecialchars($_POST['instructions']) ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger">Update Recipe</button>
                            <a href="view.php?id=<?= $id ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>