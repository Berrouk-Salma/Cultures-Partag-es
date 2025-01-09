<?php
// views/admin/dashboard.php
session_start();
require_once '../../config/db.php';

// Verify admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Get counts for dashboard stats
$stats = [
    'pending_articles' => 0,
    'total_categories' => 0,
    'total_authors' => 0,
    'total_tags' => 0
];

try {
    // Get pending articles count
    $stmt = $conn->query("SELECT COUNT(*) FROM articles WHERE is_published = 0");
    $stats['pending_articles'] = $stmt->fetchColumn();

    // Get categories count
    $stmt = $conn->query("SELECT COUNT(*) FROM categories");
    $stats['total_categories'] = $stmt->fetchColumn();

    // Get authors count
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'Auteur'");
    $stats['total_authors'] = $stmt->fetchColumn();

    // Get tags count
    $stmt = $conn->query("SELECT COUNT(*) FROM tags");
    $stats['total_tags'] = $stmt->fetchColumn();
} catch(PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - ArtCulture</title>
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
                    <a href="dashboard.php" class="flex items-center px-4 py-2.5 bg-indigo-900 rounded-lg">
                        <i class="fas fa-chart-line mr-3"></i>
                        Tableau de bord
                    </a>
                    <a href="pending-articles.php" class="flex items-center px-4 py-2.5 text-indigo-200 hover:bg-indigo-700 rounded-lg transition-colors">
                        <i class="fas fa-clock mr-3"></i>
                        Articles en attente
                        <?php if($stats['pending_articles'] > 0): ?>
                            <span class="ml-auto bg-red-500 text-white px-2 py-1 rounded-full text-xs">
                                <?php echo $stats['pending_articles']; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <a href="categories.php" class="flex items-center px-4 py-2.5 text-indigo-200 hover:bg-indigo-700 rounded-lg transition-colors">
                        <i class="fas fa-folder mr-3"></i>
                        Catégories
                    </a>
                    <a href="tags.php" class="flex items-center px-4 py-2.5 text-indigo-200 hover:bg-indigo-700 rounded-lg transition-colors">
                        <i class="fas fa-tags mr-3"></i>
                        Tags
                    </a>
                    <a href="authors.php" class="flex items-center px-4 py-2.5 text-indigo-200 hover:bg-indigo-700 rounded-lg transition-colors">
                        <i class="fas fa-users mr-3"></i>
                        Auteurs
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
        <div class="flex-1">
            <!-- Top Stats -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Pending Articles Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-orange-100 rounded-full">
                                <i class="fas fa-clock text-orange-600"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Articles en attente</h3>
                                <p class="text-2xl font-semibold"><?php echo $stats['pending_articles']; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Categories Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-full">
                                <i class="fas fa-folder text-blue-600"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Catégories</h3>
                                <p class="text-2xl font-semibold"><?php echo $stats['total_categories']; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Authors Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-full">
                                <i class="fas fa-users text-green-600"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Auteurs</h3>
                                <p class="text-2xl font-semibold"><?php echo $stats['total_authors']; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tags Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-full">
                                <i class="fas fa-tags text-purple-600"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Tags</h3>
                                <p class="text-2xl font-semibold"><?php echo $stats['total_tags']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Pending Articles -->
                <div class="mt-8">
                    <h2 class="text-2xl font-bold mb-6">Articles récents en attente</h2>
                    <div class="bg-white rounded-lg shadow overflow-x-auto">
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
                                <?php
                                // Get recent pending articles
                                try {
                                    $sql = "SELECT a.*, u.prenom, u.nom, c.name as category_name 
                                           FROM articles a 
                                           JOIN users u ON a.user_id = u.id 
                                           JOIN categories c ON a.category_id = c.id 
                                           WHERE a.is_published = 0 
                                           ORDER BY a.created_at DESC 
                                           LIMIT 5";
                                    $stmt = $conn->query($sql);
                                    $pendingArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach($pendingArticles as $article):
                                ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($article['title']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($article['prenom'] . ' ' . $article['nom']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($article['category_name']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <?php echo date('d/m/Y', strtotime($article['created_at'])); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="view-article.php?id=<?php echo $article['id']; ?>" 
                                               class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="../../action/approveArticle.php?id=<?php echo $article['id']; ?>" 
                                               class="text-green-600 hover:text-green-900 mr-3">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="../../action/rejectArticle.php?id=<?php echo $article['id']; ?>" 
                                               onclick="return confirm('Êtes-vous sûr de vouloir rejeter cet article ?')"
                                               class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                    endforeach;
                                } catch(PDOException $e) {
                                    echo "<tr><td colspan='5' class='px-6 py-4 text-center text-red-500'>Erreur lors du chargement des articles</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>