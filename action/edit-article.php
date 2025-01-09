<?php
// views/auteur/edit-article.php
session_start();
require_once '../config/db.php';

// Check if article ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID de l'article non spécifié";
    header("Location: dashboard.php");
    exit();
}

$articleId = (int)$_GET['id'];

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Get article data
    $sql = "SELECT * FROM articles WHERE id = :id AND user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $articleId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $_SESSION['id_user'], PDO::PARAM_INT);
    $stmt->execute();
    
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        $_SESSION['error'] = "Article non trouvé ou accès non autorisé";
        header("Location: dashboard.php");
        exit();
    }

    // Get categories for dropdown
    $sql = "SELECT id, name FROM categories WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['id_user'], PDO::PARAM_INT);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $_SESSION['error'] = "Erreur: " . $e->getMessage();
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'article - ArtCulture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Include your sidebar here -->
        
        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Modifier l'article</h2>
                        <a href="dashboard.php" class="text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-arrow-left mr-2"></i>Retour
                        </a>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <?php 
                            echo htmlspecialchars($_SESSION['error']);
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <form action="./updateArticle.php" method="POST" class="space-y-6">
                        <input type="hidden" name="article_id" value="<?php echo $articleId; ?>">
                        
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                            <input type="text" name="title" id="title" 
                                   value="<?php echo htmlspecialchars($article['title']); ?>"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Catégorie</label>
                            <select name="category_id" id="category_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"
                                            <?php echo ($category['id'] == $article['category_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Content -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Contenu</label>
                            <textarea name="content" id="content" rows="10" required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><?php echo htmlspecialchars($article['content']); ?></textarea>
                        </div>

                        <!-- Published Status -->
                        <div class="flex items-center">
                            <input type="checkbox" name="is_published" id="is_published" value="1"
                                   <?php echo $article['is_published'] ? 'checked' : ''; ?>
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <label for="is_published" class="ml-2 block text-sm text-gray-700">
                                Publié
                            </label>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="dashboard.php" 
                               class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
                                Annuler
                            </a>
                            <button type="submit" name="updateArticle"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>