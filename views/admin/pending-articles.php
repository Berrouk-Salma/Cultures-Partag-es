<?php
// views/admin/pending-articles.php
session_start();
require_once '../../config/db.php';
require_once '../../classes/article.php';

// Verify admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // header('Location: ../../auth/login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

$articles = new Article();
$pend = $articles->getArticlesByStatus('pending'); // Fetch pending articles

// print_r($pend);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles en attente - Admin</title>
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
                       class="flex items-center px-4 py-2.5 bg-indigo-900 rounded-lg">
                        <i class="fas fa-clock mr-3"></i>
                        Articles en attente
                    </a>
                    <a href="categories.php" 
                       class="flex items-center px-4 py-2.5 text-indigo-200 hover:bg-indigo-700 rounded-lg transition-colors">
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
                <h1 class="text-2xl font-bold text-gray-900">Articles en attente d'approbation</h1>
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
                                Titre
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Auteur
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Catégorie
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date de soumission
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($pend)): ?>
                            <?php foreach ($pend as $pd): ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($pd['title']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs">
                                            <?php echo htmlspecialchars(substr($pd['content'], 0, 100)) . '...'; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <?php echo htmlspecialchars($pd['name'] . ' ' . $pd['lastname']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo htmlspecialchars($pd['category_name']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('d/m/Y H:i', strtotime($pd['date_pub'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="#" onclick="viewArticle(<?php echo $pd['id']; ?>)" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="../../action/approveArticle.php?id=<?php echo $pd['id']; ?>" 
                                           class="text-green-600 hover:text-green-900 mr-3">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <a href="../../action/rejectArticle.php?id=<?php echo $pd['id']; ?>" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir rejeter cet article ?')"
                                           class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Aucun article en attente d'approbation
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Article View Modal -->
    <div id="articleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-4/5 lg:w-2/3 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold" id="modalTitle"></h3>
                <button onclick="closeModal()" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modalContent" class="mt-4"></div>
        </div>
    </div>

    <script>
    function viewArticle(id) {
        fetch(`../../action/getArticle.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').textContent = data.title;
                document.getElementById('modalContent').innerHTML = `
                    <div class="prose max-w-none">
                        <p class="text-gray-600 mb-4">
                            Catégorie: ${data.category_name} | 
                            Auteur: ${data.author_name} | 
                            Date: ${data.created_at}
                        </p>
                        <div class="whitespace-pre-wrap">
                            ${data.content}
                        </div>
                    </div>
                `;
                document.getElementById('articleModal').classList.remove('hidden');
            });
    }

    function closeModal() {
        document.getElementById('articleModal').classList.add('hidden');
    }

    document.getElementById('articleModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    </script>
</body>
</html>