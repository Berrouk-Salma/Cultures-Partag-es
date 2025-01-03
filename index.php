<?php
require_once './classes/database.php';
require_once './classes/article.php';
require_once './classes/category.php';


$articleObj = new Article();
$categoryObj = new Category();


$latestArticles = $articleObj->getAll(); 


$categories = $categoryObj->getAll();


session_start();
$isLoggedIn = isset($_SESSION['user_id']);
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
                    <a href="pages/articles.php" class="text-gray-600 hover:text-indigo-600">Articles</a>
                    <a href="pages/categories.php" class="text-gray-600 hover:text-indigo-600">Catégories</a>
                    <a href="#" class="text-gray-600 hover:text-indigo-600">À propos</a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if($isLoggedIn): ?>
                        <?php if($userRole === 'admin'): ?>
                            <a href="admin/dashboard.php" class="text-indigo-600 hover:text-indigo-800">Dashboard</a>
                        <?php endif; ?>
                        <a href="auth/logout.php" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Déconnexion</a>
                    <?php else: ?>
                        <a href="auth/login.php" class="text-indigo-600 hover:text-indigo-800">Connexion</a>
                        <a href="auth/register.php" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Inscription</a>
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
    <div class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Articles Récents</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($latestArticles as $article): ?>
                <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition duration-300">
                    <img src="/api/placeholder/800/400" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($article['title']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars(substr($article['content'], 0, 100)) . '...'; ?></p>
                        <a href="pages/articles.php?id=<?php echo $article['id']; ?>" class="text-indigo-600 font-semibold hover:text-indigo-800">Lire plus →</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Rest of the HTML remains the same -->
    
</body>
</html>