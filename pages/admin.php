<?php
session_start();

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

require_once  __DIR__ . '/../classes/db.php';
require_once  __DIR__ . '/../classes/article.php';
require_once  __DIR__ . '/../classes/category.php';
require_once  __DIR__ . '/../classes/user.php';

$articleObj = new Article();
$categoryObj = new Category();
$userObj = new User();

// Récupérer les statistiques
$totalArticles = count($articleObj->getAll());
$totalCategories = count($categoryObj->getAll());
$totalUsers = count($userObj->getAllUsers());
$pendingArticles = $articleObj->getPendingArticles();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - ArtCulture</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="../index.php" class="text-2xl font-bold text-indigo-600">ArtCulture</a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Admin Dashboard</span>
                    <a href="../auth/logout.php" class="text-red-600 hover:text-red-800">Déconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Articles Total</h3>
                <p class="text-3xl font-bold text-indigo-600"><?php echo $totalArticles; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Catégories</h3>
                <p class="text-3xl font-bold text-indigo-600"><?php echo $totalCategories; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Utilisateurs</h3>
                <p class="text-3xl font-bold text-indigo-600"><?php echo $totalUsers; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-2">En attente</h3>
                <p class="text-3xl font-bold text-yellow-600"><?php echo count($pendingArticles); ?></p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Quick Links -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Actions Rapides</h2>
                <div class="grid grid-cols-2 gap-4">
                    <a href="articles.php" class="bg-indigo-600 text-white p-4 rounded-lg text-center hover:bg-indigo-700">
                        Gérer les Articles
                    </a>
                    <a href="categories.php" class="bg-indigo-600 text-white p-4 rounded-lg text-center hover:bg-indigo-700">
                        Gérer les Catégories
                    </a>
                    <a href="users.php" class="bg-indigo-600 text-white p-4 rounded-lg text-center hover:bg-indigo-700">
                        Gérer les Utilisateurs
                    </a>
                    <a href="#" onclick="showCreateCategoryModal()" class="bg-green-600 text-white p-4 rounded-lg text-center hover:bg-green-700">
                        Nouvelle Catégorie
                    </a>
                </div>
            </div>

            <!-- Articles en attente -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Articles en Attente</h2>
                <div class="space-y-4">
                    <?php foreach($pendingArticles as $article): ?>
                    <div class="flex items-center justify-between border-b pb-2">
                        <div>
                            <h3 class="font-medium"><?php echo htmlspecialchars($article['title']); ?></h3>
                            <p class="text-sm text-gray-600">par <?php echo htmlspecialchars($article['author_name']); ?></p>
                        </div>
                        <div class="flex space-x-2">
                            <form method="POST" action="../action.php">
                                <input type="hidden" name="action" value="publish_article">
                                <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                                <button type="submit" class="text-green-600 hover:text-green-900">Publier</button>
                            </form>
                            <a href="../edit.php?id=<?php echo $article['id']; ?>" class="text-blue-600 hover:text-blue-900">
                                Réviser
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Activité Récente</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php 
                        // Ici vous pouvez ajouter une table d'activités dans la base de données
                        // et afficher les dernières activités
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Category Modal -->
    <div id="createCategoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Nouvelle Catégorie</h3>
                <form method="POST" action="../action.php">
                    <input type="hidden" name="action" value="create_category">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nom</label>
                        <input type="text" name="name" required 
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" rows="3"
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="hideCreateCategoryModal()" 
                                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 mr-2">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showCreateCategoryModal() {
            document.getElementById('createCategoryModal').classList.remove('hidden');
        }
        
        function hideCreateCategoryModal() {
            document.getElementById('createCategoryModal').classList.add('hidden');
        }
    </script>
</body>
</html>