<?php
require 'config.php';

$search   = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$filter   = $_GET['filter'] ?? '';

$sql = "SELECT * FROM recipes WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (title LIKE ? OR ingredients LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}
if ($filter === 'favorite') {
    $sql .= " AND is_favorite = 1";
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recipes = $stmt->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM recipes ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Recipe Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #e74c3c;
            --primary-dark: #c0392b;
        }
        body { background: #f8f9fa; }
        .navbar { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .recipe-card {
            transition: all 0.3s ease;
            border: none;
            overflow: hidden;
        }
        .recipe-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12) !important;
        }
        .recipe-img {
            height: 200px;
            object-fit: cover;
            background: #f1f3f5;
        }
        .badge-difficulty-easy { background: #27ae60; }
        .badge-difficulty-medium { background: #f39c12; }
        .badge-difficulty-hard { background: #e74c3c; }
        .favorite-btn { cursor: pointer; font-size: 1.3rem; }
        .rating { color: #f1c40f; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark mb-4 shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-journal-richtext"></i> My Recipe Book
        </a>
        <div class="d-flex gap-2">
            <a href="random.php" class="btn btn-light btn-sm">
                <i class="bi bi-shuffle"></i> Random
            </a>
            <a href="add.php" class="btn btn-warning btn-sm fw-bold">
                <i class="bi bi-plus-lg"></i> Add Recipe
            </a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small text-muted">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search title or ingredients..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Filter</label>
                    <select name="filter" class="form-select">
                        <option value="">All Recipes</option>
                        <option value="favorite" <?= $filter === 'favorite' ? 'selected' : '' ?>>Favorites only</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <?php if (count($recipes) === 0): ?>
        <div class="text-center py-5">
            <i class="bi bi-journal-x display-1 text-muted"></i>
            <h3 class="mt-3 text-muted">No recipes found</h3>
            <a href="add.php" class="btn btn-danger mt-3">Add your first recipe</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($recipes as $recipe): ?>
                <div class="col">
                    <div class="card h-100 recipe-card shadow-sm">
                        <?php if ($recipe['image'] && file_exists('uploads/' . $recipe['image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($recipe['image']) ?>" class="recipe-img card-img-top" alt="">
                        <?php else: ?>
                            <div class="recipe-img d-flex align-items-center justify-content-center text-muted">
                                <i class="bi bi-image" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>

                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0"><?= htmlspecialchars($recipe['title']) ?></h5>
                                <a href="toggle_favorite.php?id=<?= $recipe['id'] ?>" class="favorite-btn text-decoration-none">
                                    <?php if ($recipe['is_favorite']): ?>
                                        <i class="bi bi-heart-fill text-danger"></i>
                                    <?php else: ?>
                                        <i class="bi bi-heart text-secondary"></i>
                                    <?php endif; ?>
                                </a>
                            </div>

                            <div class="mb-2">
                                <span class="badge bg-secondary"><?= htmlspecialchars($recipe['category']) ?></span>
                                <span class="badge badge-difficulty-<?= strtolower($recipe['difficulty']) ?>">
                                    <?= $recipe['difficulty'] ?>
                                </span>
                            </div>

                            <div class="small text-muted mb-2">
                                <?php if ($recipe['prep_time']): ?>
                                    <i class="bi bi-clock"></i> Prep: <?= htmlspecialchars($recipe['prep_time']) ?>
                                <?php endif; ?>
                                <?php if ($recipe['cook_time']): ?>
                                    · <i class="bi bi-fire"></i> Cook: <?= htmlspecialchars($recipe['cook_time']) ?>
                                <?php endif; ?>
                                <?php if ($recipe['servings']): ?>
                                    · <i class="bi bi-people"></i> <?= htmlspecialchars($recipe['servings']) ?>
                                <?php endif; ?>
                            </div>

                            <div class="rating mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= $recipe['rating'] ? '-fill' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0 d-flex justify-content-between">
                            <a href="view.php?id=<?= $recipe['id'] ?>" class="btn btn-sm btn-outline-danger">View Recipe</a>
                            <div>
                                <a href="edit.php?id=<?= $recipe['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="delete.php?id=<?= $recipe['id'] ?>" class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this recipe?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>