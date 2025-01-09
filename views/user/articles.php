<?php
session_start();
require_once '../../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Get all published articles with like and comment info
    $query = "SELECT 
                a.*, 
                u.name as author_name,
                c.name as category_name,
                (SELECT COUNT(*) FROM article_likes WHERE article_id = a.id) as like_count,
                (SELECT COUNT(*) FROM article_comments WHERE article_id = a.id) as comment_count,
                (SELECT COUNT(*) > 0 FROM article_likes WHERE article_id = a.id AND user_id = :user_id) as has_liked
             FROM articles a
             JOIN users u ON a.user_id = u.id
             JOIN categories c ON a.category_id = c.id
             WHERE a.is_published = true
             ORDER BY a.published_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute(['user_id' => $_SESSION['id_user']]);
    $articles = $stmt->fetchAll();

    // Get comments for all articles
    $comments = [];
    if (!empty($articles)) {
        $articleIds = array_column($articles, 'id');
        $commentsQuery = "SELECT 
                            ac.*,
                            u.name as user_name,
                            a.id as article_id
                         FROM article_comments ac
                         JOIN users u ON ac.user_id = u.id
                         JOIN articles a ON ac.article_id = a.id
                         WHERE ac.article_id IN (" . implode(',', array_fill(0, count($articleIds), '?')) . ")
                         ORDER BY ac.created_at DESC";
        
        $commentsStmt = $conn->prepare($commentsQuery);
        $commentsStmt->execute($articleIds);
        $allComments = $commentsStmt->fetchAll();
        
        // Group comments by article
        foreach ($allComments as $comment) {
            $comments[$comment['article_id']][] = $comment;
        }
    }

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
        <!-- Articles List -->
        <div class="space-y-8">
            <?php foreach ($articles as $article): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <!-- Article Header -->
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">
                                <?php echo htmlspecialchars($article['title']); ?>
                            </h2>
                            <p class="text-gray-600 mt-1">
                                Par <?php echo htmlspecialchars($article['author_name']); ?> • 
                                <?php echo htmlspecialchars($article['category_name']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Article Content -->
                    <div class="prose max-w-none mb-6">
                        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                    </div>

                    <!-- Like and Comment Section -->
                    <div class="border-t pt-4">
                        <div class="flex items-center justify-between mb-4">
                            <button 
                                onclick="likeArticle(<?php echo $article['id']; ?>)"
                                class="flex items-center space-x-2 <?php echo $article['has_liked'] ? 'text-red-500' : 'text-gray-500'; ?>"
                            >
                                <svg class="w-6 h-6" fill="<?php echo $article['has_liked'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span id="like-count-<?php echo $article['id']; ?>">
                                    <?php echo $article['like_count']; ?>
                                </span>
                            </button>
                            
                            <button 
                                onclick="toggleComments(<?php echo $article['id']; ?>)"
                                class="text-gray-500 hover:text-gray-700"
                            >
                                Commentaires (<?php echo $article['comment_count']; ?>)
                            </button>
                        </div>

                        <!-- Comments Section -->
                        <div id="comments-section-<?php echo $article['id']; ?>" class="hidden">
                            <!-- Comment Form -->
                            <form onsubmit="submitComment(event, <?php echo $article['id']; ?>)" class="mb-4">
                                <textarea 
                                    name="content"
                                    class="w-full p-2 border rounded-md"
                                    placeholder="Ajouter un commentaire..."
                                    required
                                ></textarea>
                                <button 
                                    type="submit"
                                    class="mt-2 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                                >
                                    Commenter
                                </button>
                            </form>

                            <!-- Comments List -->
                            <div id="comments-list-<?php echo $article['id']; ?>" class="space-y-4">
                                <?php if (isset($comments[$article['id']])): ?>
                                    <?php foreach ($comments[$article['id']] as $comment): ?>
                                        <div class="border-b pb-3">
                                            <div class="flex justify-between items-center">
                                                <span class="font-medium">
                                                    <?php echo htmlspecialchars($comment['user_name']); ?>
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>
                                                </span>
                                            </div>
                                            <p class="mt-1 text-gray-600">
                                                <?php echo htmlspecialchars($comment['content']); ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    function likeArticle(articleId) {
        fetch('../../action/likeArticle.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `article_id=${articleId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const likeButton = document.querySelector(`button[onclick="likeArticle(${articleId})"]`);
                const likeCount = document.getElementById(`like-count-${articleId}`);
                likeCount.textContent = data.likes;
                if (data.liked) {
                    likeButton.classList.remove('text-gray-500');
                    likeButton.classList.add('text-red-500');
                    likeButton.querySelector('svg').setAttribute('fill', 'currentColor');
                } else {
                    likeButton.classList.remove('text-red-500');
                    likeButton.classList.add('text-gray-500');
                    likeButton.querySelector('svg').setAttribute('fill', 'none');
                }
            }
        });
    }

    function toggleComments(articleId) {
        const commentsSection = document.getElementById(`comments-section-${articleId}`);
        commentsSection.classList.toggle('hidden');
    }

    function submitComment(event, articleId) {
        event.preventDefault();
        const form = event.target;
        const content = form.content.value;

        fetch('../../action/addComment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `article_id=${articleId}&content=${encodeURIComponent(content)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                form.reset();
                location.reload(); // Refresh to show new comment
            }
        });
    }
    </script>
</body>
</html>