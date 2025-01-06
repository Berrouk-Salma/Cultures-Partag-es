<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once  __DIR__ . '/../classes/db.php';
require_once  __DIR__ . '/../classes/article.php';
require_once  __DIR__ . '/../classes/category.php';

$articleObj = new Article();
$categoryObj = new Category();

$myArticles = $articleObj->getUserArticles($_SESSION['user_id']);
$categories = $categoryObj->getAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Articles - ArtCulture</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Mes Articles</h1>
            <button onclick="showCreateForm()" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Nouvel Article
            </button>
        </div>

        <!-- Liste des articles -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($myArticles as $article): ?>
                    <tr>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($article['title']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($article['category_name']); ?></td>
                        <td class="px-6 py-4">
                            <?php if($article['is_published']): ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Publié</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($article['created_at'])); ?></td>
                        <td class="px-6 py-4">
                            <a href="../edit.php?id=<?php echo $article['id']; ?>" 
                               class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</a>
                            
                            <form method="POST" action="../action.php" class="inline">
                                <input type="hidden" name="action" value="delete_article">
                                <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Voulez-vous vraiment supprimer cet article ?')">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal de création d'article -->
        <div id="createModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <form method="POST" action="../action.php">
                    <input type="hidden" name="action" value="submit_article">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Titre</label>
                        <input type="text" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Catégorie</label>
                        <select name="category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Contenu</label>
                        <textarea name="content" rows="6" required 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="hideCreateForm()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Soumettre</button>
                    </div>
                </form>
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