<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/Article.php';

// Check login
if(!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$article = new Article();
$articles = $article->getAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Articles</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
</head>
<body>
    <div class="p-6">
        <h1>Articles</h1>
        
        <!-- Si user author wla admin -->
        <?php if($_SESSION['role'] == 'author' || $_SESSION['role'] == 'admin'): ?>
            <form method="POST" class="mb-4">
                <input type="text" name="title" placeholder="Titre" required>
                <textarea name="content" placeholder="Contenu" required></textarea>
                <button type="submit">Ajouter Article</button>
            </form>
        <?php endif; ?>

        <!-- Liste des articles -->
        <div>
            <?php foreach($articles as $article): ?>
                <div class="mb-4">
                    <h3><?php echo $article['title']; ?></h3>
                    <p><?php echo $article['content']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>