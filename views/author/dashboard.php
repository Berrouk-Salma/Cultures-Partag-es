<?php
session_start();

require_once '../../classes/Article.php';
require_once '../../classes/category.php';



// Get author's articles
$article = new Article();
$articles = $article->getArticlesByAuthor($_SESSION['id_user']);

$category = new Category();
$categories = $category->getCategorie($_SESSION['id_user']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Auteur - ArtCulture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-indigo-800 text-white w-64 py-6 flex flex-col">
            <div class="px-6 py-4">
                <h2 class="text-2xl font-semibold">ArtCulture</h2>
                <p class="text-sm text-indigo-200">Dashboard Auteur</p>
            </div>
            
            <nav class="mt-6 flex-1">
                <div class="px-4 space-y-3">
                    <a href="dashboard.php" 
                       class="flex items-center px-4 py-2.5 bg-indigo-900 rounded-lg">
                        <i class="fas fa-newspaper mr-3"></i>
                        Mes Articles
                    </a>

                    <a href="categories.php" 
           class="flex items-center px-4 py-2.5 bg-indigo-900 rounded-lg">
            <i class="fas fa-tags mr-3"></i>
            Mes Catégories
        </a>  
                    <a href="add-article.php" 
                       class="flex items-center px-4 py-2.5 text-indigo-200 hover:bg-indigo-700 rounded-lg transition-colors">
                        <i class="fas fa-plus-circle mr-3"></i>
                        Nouvel Article
                    </a>
                </div>
            </nav>
            <div class="px-6 py-4 border-t border-indigo-700">
                <div class="flex items-center">
                    <i class="fas fa-user-circle text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm font-medium"><?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?></p>
                        <p class="text-xs text-indigo-200"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                    </div>
                </div>
                <a href="../../auth/logout.php" 
                   class="mt-4 block text-center px-4 py-2 bg-indigo-700 hover:bg-indigo-600 rounded-lg text-sm transition-colors">
                    Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Navigation -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Mes Articles</h1>
                    <div class=" flex flex-row gap-2">
                    <button onclick="document.getElementById('articleModal').classList.remove('hidden')"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors flex items-center">
    <i class="fas fa-plus mr-2"></i>
    Nouvel Article
</button>
                    <button onclick="document.getElementById('categoryModal').classList.remove('hidden')"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors flex items-center">
    <i class="fas fa-plus mr-2"></i>
    Nouvelle Catégorie
</button>
</div>
                </div>
            </header>

            <!-- Articles Grid -->
            <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        <?php 
                        echo htmlspecialchars($_SESSION['success']);
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <?php 
                        echo htmlspecialchars($_SESSION['error']);
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Titre
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Contenu
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Catégorie
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date de création
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Statut
                </th>
                <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if ($articles && count($articles) > 0): ?>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($article['title']); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500 max-w-xs truncate">
                                <?php echo htmlspecialchars(substr($article['content'], 0, 100)) . '...'; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">
                                <?php echo htmlspecialchars($article['category_name'] ?? 'Non catégorisé'); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                <?php echo date('d/m/Y', strtotime($article['created_at'])); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php echo $article['is_published'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                <?php echo $article['is_published'] ? 'Publié' : 'Brouillon'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="edit-article.php?id=<?php echo $article['id']; ?>" 
                               class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="../../action/deletearticle.php?id=<?php echo $article['id']; ?>" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')"
                               class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center">
                        <div class="text-center py-8">
                            <i class="fas fa-newspaper text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600">Vous n'avez pas encore créé d'articles.</p>
                            <button onclick="document.getElementById('articleModal').classList.remove('hidden')"
                                    class="mt-4 inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                                Créer mon premier article
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
            </main>
        </div>
    </div>
    <div id="categoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Ajouter une Catégorie</h3>
            
            <!-- Form -->
            <form action="../../action/addCategorie.php" method="POST" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" name="name" id="name" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="document.getElementById('categoryModal').classList.add('hidden')"
                            class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" name="addCategory"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add this JavaScript for handling modal close when clicking outside -->
<script>
document.getElementById('categoryModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
<div id="articleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[800px] shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Ajouter un Article</h3>
            
            <!-- Form -->
            <form action="../../action/addArticle.php" method="POST" class="space-y-4">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                    <input type="text" name="title" id="title" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select name="category_id" id="category_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Sélectionnez une catégorie</option>
                        <?php 
                            foreach($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach;
                     
                        ?>
                    </select>
                </div>
                
                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Contenu</label>
                    <textarea name="content" id="content" rows="6" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>

                <!-- Published Status -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_published" id="is_published" value="1"
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <label for="is_published" class="ml-2 block text-sm text-gray-700">
                        Publier immédiatement
                    </label>
                </div>

                <!-- Hidden Fields -->
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['id_user']; ?>">

                <!-- Buttons -->
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" 
                            onclick="document.getElementById('articleModal').classList.add('hidden')"
                            class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" name="addArticle"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        Publier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add this JavaScript for handling modal close when clicking outside -->
<script>
document.getElementById('articleModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
</body>
</html>