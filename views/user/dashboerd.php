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

    // Get user's likes
    $likesQuery = "SELECT 
                    a.id, 
                    a.title, 
                    a.published_at,
                    u.name as author_name,
                    c.name as category_name
                   FROM article_likes al
                   JOIN articles a ON al.article_id = a.id
                   JOIN users u ON a.user_id = u.id
                   JOIN categories c ON a.category_id = c.id
                   WHERE al.user_id = :user_id
                   ORDER BY al.created_at DESC";
    
    $likesStmt = $conn->prepare($likesQuery);
    $likesStmt->execute(['user_id' => $_SESSION['id_user']]);
    $likedArticles = $likesStmt->fetchAll();

    // Get user's comments
    $commentsQuery = "SELECT 
                        ac.content as comment_content,
                        ac.created_at as comment_date,
                        a.id as article_id,
                        a.title as article_title
                     FROM article_comments ac
                     JOIN articles a ON ac.article_id = a.id
                     WHERE ac.user_id = :user_id
                     ORDER BY ac.created_at DESC";
    
    $commentsStmt = $conn->prepare($commentsQuery);
    $commentsStmt->execute(['user_id' => $_SESSION['id_user']]);
    $userComments = $commentsStmt->fetchAll();

} catch (Exception $e) {
    $_SESSION['error'] = "Erreur: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - <?php echo htmlspecialchars($_SESSION['name'] ?? 'Utilisateur'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
   

    <div class="container mx-auto px-4 py-8">
        <!-- User Profile Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Bienvenue, <?php echo htmlspecialchars($_SESSION['name'] ?? 'Utilisateur'); ?>
                    </h1>
                    <p class="text-gray-600"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
                </div>
                <div>
                    <a href="edit-profile.php" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        Modifier le profil
                    </a>
                </div>
            </div>
        </div>

        <!-- Activity Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Articles aimés</h2>
                <p class="text-3xl font-bold text-indigo-600"><?php echo count($likedArticles); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Commentaires</h2>
                <p class="text-3xl font-bold text-indigo-600"><?php echo count($userComments); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Dernière activité</h2>
                <p class="text-gray-600">
                    <?php
                    $lastActivity = max(
                        $likedArticles ? strtotime($likedArticles[0]['published_at'] ?? '0') : 0,
                        $userComments ? strtotime($userComments[0]['comment_date'] ?? '0') : 0
                    );
                    echo $lastActivity ? date('d/m/Y H:i', $lastActivity) : 'Aucune activité';
                    ?>
                </p>
            </div>
        </div>

        <!-- Liked Articles Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Articles aimés</h2>
            <div class="divide-y divide-gray-200">
                <?php foreach ($likedArticles as $article): ?>
                    <div class="py-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <a href="../article.php?id=<?php echo $article['id']; ?>" 
                                   class="text-lg font-medium text-indigo-600 hover:text-indigo-800">
                                    <?php echo htmlspecialchars($article['title']); ?>
                                </a>
                                <p class="text-sm text-gray-600">
                                    Par <?php echo htmlspecialchars($article['author_name']); ?> dans 
                                    <?php echo htmlspecialchars($article['category_name']); ?>
                                </p>
                            </div>
                            <span class="text-sm text-gray-500">
                                <?php echo date('d/m/Y', strtotime($article['published_at'])); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($likedArticles)): ?>
                    <p class="text-gray-500 py-4">Vous n'avez pas encore aimé d'articles.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Vos commentaires</h2>
            <div class="divide-y divide-gray-200">
                <?php foreach ($userComments as $comment): ?>
                    <div class="py-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <a href="../article.php?id=<?php echo $comment['article_id']; ?>" 
                                   class="text-lg font-medium text-indigo-600 hover:text-indigo-800">
                                    <?php echo htmlspecialchars($comment['article_title']); ?>
                                </a>
                                <p class="text-gray-600 mt-2">
                                    <?php echo htmlspecialchars($comment['comment_content']); ?>
                                </p>
                            </div>
                            <span class="text-sm text-gray-500">
                                <?php echo date('d/m/Y H:i', strtotime($comment['comment_date'])); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($userComments)): ?>
                    <p class="text-gray-500 py-4">Vous n'avez pas encore commenté d'articles.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>