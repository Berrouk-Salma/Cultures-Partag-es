<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../../config/db.php';


if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    // header('Location: ../../auth/login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();


try {
    $sql = "SELECT c.name AS cat_name, u.name AS admin_name, u.lastname , description, created_at, u.id
            FROM categories c 
            LEFT JOIN users u ON c.user_id = u.id 
            ORDER BY c.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $_SESSION['error'] = "Erreur lors du chargement des catégories: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Catégories - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-indigo-800 text-white w-64 py-6 flex flex-col">
            <div class="px-6 py-4">
                <h2 class="text-2xl font-semibold">ArtCulture</h2>
                <p class="text-sm text-indigo-200">Dashboard Admin</p>
            </div>
            
            <nav class="mt-6 flex-1">
                <div class="px-4 space-y-3">
                    <a href="dashboard.php" 
                       class="flex items-center px-4 py-2.5 text-indigo-200 hover:bg-indigo-700 rounded-lg transition-colors">
                        <i class="fas fa-chart-line mr-3"></i>
                        Tableau de bord
                    </a>
                    <a href="pending-articles.php" 
                       class="flex items-center px-4 py-2.5 text-indigo-200 hover:bg-indigo-700 rounded-lg transition-colors">
                        <i class="fas fa-clock mr-3"></i>
                        Articles en attente
                    </a>
                    <a href="categories.php" 
                       class="flex items-center px-4 py-2.5 bg-indigo-900 rounded-lg">
                        <i class="fas fa-folder mr-3"></i>
                        Catégories
                    </a>
                    <a href="tags.php" 
                       class="flex items-center px-4 py-2.5 text-indigo-200 hover:bg-indigo-700 rounded-lg transition-colors">
                        <i class="fas fa-tags mr-3"></i>
                        Tags
                    </a>
                </div>
            </nav>
            
            <div class="px-6 py-4 border-t border-indigo-700">
                <div class="flex items-center">
                    <i class="fas fa-user-shield text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm font-medium">
                            <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?>
                        </p>
                        <p class="text-xs text-indigo-200">Administrateur</p>
                    </div>
                </div>
                <a href="../../auth/logout.php" 
                   class="mt-4 block text-center px-4 py-2 bg-indigo-700 hover:bg-indigo-600 rounded-lg text-sm transition-colors">
                    Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Gestion des Catégories</h1>
                <button onclick="document.getElementById('categoryModal').classList.remove('hidden')"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Nouvelle Catégorie
                </button>
            </div>

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

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Créé par
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date de création
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($category['cat_name']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500 max-w-xs truncate">
                                            <?php echo htmlspecialchars($category['description']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo htmlspecialchars($category['admin_name'] . ' ' . $category['lastname']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('d/m/Y', strtotime($category['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="../../action/deleteCategory.php?id=<?php echo $category['id']; ?>" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')"
                                           class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Aucune catégorie trouvée
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    <div id="categoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Ajouter une Catégorie</h3>
                <form id="categoryForm" action="../../action/addCategory.php" method="POST" class="space-y-4">
                    <input type="hidden" id="categoryId" name="category_id">
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" id="name" name="name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="3" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                onclick="document.getElementById('categoryModal').classList.add('hidden')"
                                class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
                            Annuler
                        </button>
                        <button type="submit" name="submitCategory"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                            Sauvegarder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function editCategory(category) {
        document.getElementById('modalTitle').textContent = 'Modifier la Catégorie';
        document.getElementById('categoryId').value = category.id;
        document.getElementById('name').value = category.name;
        document.getElementById('description').value = category.description;
        document.getElementById('categoryForm').action = '../../action/updateCategory.php';
        document.getElementById('categoryModal').classList.remove('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('categoryModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
    </script>
</body>
</html>