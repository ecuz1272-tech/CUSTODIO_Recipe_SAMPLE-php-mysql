<?php
require 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = trim($_POST['title'] ?? '');
    $category     = trim($_POST['category'] ?? 'Other');
    $prep_time    = trim($_POST['prep_time'] ?? '');
    $cook_time    = trim($_POST['cook_time'] ?? '');
    $servings     = trim($_POST['servings'] ?? '');
    $difficulty   = $_POST['difficulty'] ?? 'Easy';
    $ingredients  = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $rating       = (int)($_POST['rating'] ?? 0);

    $imageName = null;

    
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5 * 1024 * 1024) {
            $imageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $imageName);
        } else {
            $error = 'Invalid image. Only JPG, PNG, WEBP, GIF under 5MB allowed.';
        }
    }

    if (!$error && $title && $ingredients && $instructions) {
        $stmt = $pdo->prepare("INSERT INTO recipes 
            (title, category, prep_time, cook_time, servings, difficulty, ingredients, instructions, image, rating) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $category, $prep_time, $cook_time, $servings, $difficulty, $ingredients, $instructions, $imageName, $rating]);
        header('Location: index.php');
        exit;
    } elseif (!$error) {
        $error = 'Please fill in Title, Ingredients, and Instructions.';
    }
}

$predefined = ['Main', 'Dessert', 'Appetizer', 'Breakfast', 'Salad', 'Soup', 'Snack', 'Beverage', 'Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark mb-4" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-journal-richtext"></i> My Recipe Book</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow border-0">
                <div class="card-header text-white" style="background: #e74c3c;">
                    <h4 class="mb-0">Add New Recipe</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Recipe Title *</label>
                            <input type="text" name="title" class="form-control form-control-lg" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                <input type="text" name="category" class="form-control" list="categoryList" placeholder="e.g. Main">
                                <datalist id="categoryList">
                                    <?php foreach ($predefined as $cat): ?>
                                        <option value="<?= $cat ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Difficulty</label>
                                <select name="difficulty" class="form-select">
                                    <option value="Easy">Easy</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Hard">Hard</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Your Rating</label>
                                <select name="rating" class="form-select">
                                    <option value="0">No rating</option>
                                    <option value="1">★☆☆☆☆</option>
                                    <option value="2">★★☆☆☆</option>
                                    <option value="3">★★★☆☆</option>
                                    <option value="4">★★★★☆</option>
                                    <option value="5">★★★★★</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Prep Time</label>
                                <input type="text" name="prep_time" class="form-control" placeholder="15 mins">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Cook Time</label>
                                <input type="text" name="cook_time" class="form-control" placeholder="30 mins">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Servings</label>
                                <input type="text" name="servings" class="form-control" placeholder="4 people">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Recipe Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="form-text">JPG, PNG, WEBP or GIF (max 5MB)</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ingredients *</label>
                            <textarea name="ingredients" class="form-control" rows="6" required placeholder="One ingredient per line..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Instructions *</label>
                            <textarea name="instructions" class="form-control" rows="8" required placeholder="Step-by-step instructions..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger px-4">Save Recipe</button>
                            <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>