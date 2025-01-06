<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once  __DIR__ . '/../config/db.php';
require_once  __DIR__ . '/../classes/article.php';
require_once  __DIR__ . '/../classes/category.php';


$articleObj = new Article();
$categoryObj = new Category();

// Récupérer les articles
$articles = $articleObj->getAll();
$categories = $categoryObj->getAll();

// Messages de succès/erreur
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Articles - ArtCulture</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Gestion des Articles</h1>
            <?php if($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'author'): ?>
            <a href="#" onclick="showCreateForm()" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Nouvel Article
            </a>
            <?php endif; ?>
        </div>

        <!-- Messages -->
        <?php if($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php
                switch($success) {
                    case 'created': echo "Article créé avec succès!"; break;
                    case 'updated': echo "Article mis à jour!"; break;
                    case 'deleted': echo "Article supprimé!"; break;
                    case 'published': echo "Article publié!"; break;
                    case 'approved': echo "Article approuvé!"; break;
                    case 'rejected': echo "Article rejeté!"; break;
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                Une erreur est survenue.
            </div>
        <?php endif; ?>

        <!-- Liste des articles -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auteur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($articles as $article): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($article['title']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($article['category_name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($article['author_name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $article['is_published'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                <?php echo $article['is_published'] ? 'Publié' : 'En attente'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <?php if($_SESSION['role'] === 'admin' || $article['user_id'] === $_SESSION['user_id']): ?>
                                <a href="../edit.php?id=<?php echo $article['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</a>
                                
                                <form method="POST" action="../action.php" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article?');">
                                    <input type="hidden" name="action" value="delete_article">
                                    <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                </form>
                                
                                <?php if($_SESSION['role'] === 'admin' && !$article['is_published']): ?>
                                    <form method="POST" action="../action.php" class="inline ml-3">
                                        <input type="hidden" name="action" value="approve_article">
                                        <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                                        <button type="submit" class="text-green-600 hover:text-green-900">Approuver</button>
                                    </form>

                                    <form method="POST" action="../action.php" class="inline ml-2">
                                        <input type="hidden" name="action" value="reject_article">
                                        <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900">Rejeter</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal de création d'article -->
        <div id="createModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Nouvel Article</h3>
                    <form method="POST" action="../action.php">
                        <input type="hidden" name="action" value="create_article">
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Titre</label>
                            <input type="text" name="title" required 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Catégorie</label>
                            <select name="category_id" required
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Contenu</label>
                            <textarea name="content" required rows="6"
                                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" onclick="hideCreateForm()" 
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
    </div>

    <script>
        function showCreateForm() {
            document.getElementById('createModal').classList.remove('hidden');
        }
        
        function hideCreateForm() {
            document.getElementById('createModal').classList.add('hidden');
        }
    </script>
</body>
</html>