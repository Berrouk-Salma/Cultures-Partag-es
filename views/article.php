<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Get all published articles
    $query = "SELECT 
                a.*, 
                u.name as author_name,
                c.name as category_name,
                (SELECT COUNT(*) FROM article_likes WHERE article_id = a.id) as like_count,
                (SELECT COUNT(*) FROM article_comments WHERE article_id = a.id) as comment_count
             FROM articles a
             JOIN users u ON a.user_id = u.id
             JOIN categories c ON a.category_id = c.id
             WHERE a.is_published = true
             ORDER BY a.published_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $articles = $stmt->fetchAll();

} catch (Exception $e) {
    $_SESSION['error'] = "Erreur: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Articles</h1>

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($articles as $article): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <!-- Category and Date -->
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm text-indigo-600">
                                <?php echo htmlspecialchars($article['category_name']); ?>
                            </span>
                        </div>

                        <!-- Title -->
                        <h2 class="text-xl font-bold text-gray-900 mb-2">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </h2>

                        <!-- Author -->
                        <p class="text-gray-600 text-sm mb-4">
                            Par <?php echo htmlspecialchars($article['author_name']); ?>
                        </p>

                        <!-- Content Preview -->
                        <p class="text-gray-600 mb-4">
                            <?php 
                            $preview = substr(strip_tags($article['content']), 0, 150);
                            echo htmlspecialchars($preview) . '...'; 
                            ?>
                        </p>

                        <!-- Likes and Comments Count -->
                        <div class="flex items-center justify-between mt-4 pt-4 border-t">
                            <div class="flex items-center space-x-4">
                                <span class="flex items-center text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                                    </svg>
                                    <?php echo $article['like_count']; ?>
                                </span>
                                <span class="flex items-center text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                    </svg>
                                    <?php echo $article['comment_count']; ?>
                                </span>
                            </div>
                            
                            <button 
                                onclick="window.location.href='article_detail.php?id=<?php echo $article['id']; ?>'"
                                class="text-indigo-600 hover:text-indigo-800"
                            >
                                Lire plus →
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($articles)): ?>
            <div class="text-center text-gray-600 py-8">
                Aucun article n'est disponible pour le moment.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>