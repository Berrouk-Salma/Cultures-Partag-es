

<?php
require_once __DIR__ .  "/config/db.php";
require_once __DIR__ . '/classes/article.php';
require_once __DIR__ .  '/classes/category.php';


$articleObj = new Article();
$categoryObj = new Category();






// $latestArticles = $articleObj->getAll(); 
$articles = $articleObj->ShowAllArticles();

print_r($articles);



$categories = $categoryObj->getAll();


session_start();
$isLoggedIn = isset($_SESSION['id_user']);
$userRole = $isLoggedIn ? $_SESSION['role'] : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtCulture - Plateforme Artistique et Culturelle</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="#" class="text-2xl font-bold text-indigo-600">ArtCulture</a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#" class="text-gray-600 hover:text-indigo-600">Accueil</a>
                    <a href="views/user/articles.php" class="text-gray-600 hover:text-indigo-600">Articles</a>
                    <a href="pages/categories.php" class="text-gray-600 hover:text-indigo-600">Catégories</a>
                    <a href="#" class="text-gray-600 hover:text-indigo-600">À propos</a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if($isLoggedIn): ?>
                        <?php if($userRole === 'admin'): ?>
                            <a href="admin/dashboard.php" class="text-indigo-600 hover:text-indigo-800">Dashboard</a>
                        <?php endif; ?>
                        <a href="auth/logout.php" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Déconnexion</a>
                    <?php endif; ?>
                   
                </div>
            </div>
        </div>
    </nav>

   
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-20">
       
    </div>

    <div class="max-w-7xl mx-auto px-4 py-16">
        <h2 class="text-3xl font-bold text-center mb-12">Explorez nos Catégories</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach($categories as $category): ?>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition duration-300">
                <h3 class="text-xl font-semibold mb-4"><?php echo htmlspecialchars($category['name']); ?></h3>
                <p class="text-gray-600"><?php echo htmlspecialchars($category['description'] ?? 'Découvrez les articles de cette catégorie.'); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Latest Articles -->
    <div class="container mx-auto px-4 py-8">
        <!-- Articles List -->
        <div class="space-y-8">
            <?php foreach ($articles as $article): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <!-- Article Header -->
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">
                                <?php echo htmlspecialchars($article['article_title']); ?>
                            </h2>
                            <p class="text-gray-600 mt-1">
                                Par <?php echo htmlspecialchars($article['author_name']); ?> • 
                                <?php echo htmlspecialchars($article['category_name']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Article Content -->
                    <div class="prose max-w-none mb-6">
                        <?php echo nl2br(htmlspecialchars($article['article_content'])); ?>
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

    <!-- Rest of the HTML remains the same -->
    
</body>
</html>