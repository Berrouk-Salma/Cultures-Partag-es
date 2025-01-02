<?php
require_once '../classes/Database.php';
require_once '../classes/Article.php';

$article = new Article();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $articleData = [
        'title' => $_POST['title'],
        'content' => $_POST['content'],
        'user_id' => $_SESSION['user_id'],
        'category_id' => $_POST['category_id']
    ];
    
    if ($article->create($articleData)) {
        echo "Article créé avec succès";
    }
}

$articles = $article->getAll();
foreach ($articles as $article) {
    echo "<h2>{$article['title']}</h2>";
    echo "<p>{$article['content']}</p>";
}