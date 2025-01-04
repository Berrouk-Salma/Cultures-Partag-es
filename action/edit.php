<?php
session_start();

if (!isset($_SESSION['user_id'])) {
   header('Location: auth/login.php');
   exit();
}

require_once 'classes/database.php';
require_once 'classes/article.php';
require_once 'classes/category.php';

$articleObj = new Article();
$categoryObj = new Category();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
   header('Location: pages/articles.php');
   exit();
}

$article = $articleObj->getById($id);
if (!$article) {
   header('Location: pages/articles.php');
   exit();
}

// Vérifier les droits d'édition
if ($_SESSION['role'] !== 'admin' && $article['user_id'] !== $_SESSION['user_id']) {
   header('Location: pages/articles.php');
   exit();
}

$categories = $categoryObj->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
   $content = $_POST['content'];
   $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
   
   $data = [
       'title' => $title,
       'content' => $content,
       'category_id' => $category_id
   ];
   
   $result = $articleObj->update($id, $data, $_SESSION['user_id']);
   
   if ($result) {
       header('Location: pages/articles.php?success=1');
       exit();
   } else {
       $error = "Erreur lors de la modification de l'article";
   }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Modifier l'Article - ArtCulture</title>
   <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
   <div class="max-w-4xl mx-auto px-4 py-8">
       <div class="bg-white rounded-lg shadow-lg p-6">
           <h2 class="text-2xl font-bold mb-6">Modifier l'article</h2>
           
           <?php if (isset($error)): ?>
               <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                   <?php echo htmlspecialchars($error); ?>
               </div>
           <?php endif; ?>

           <form method="POST" action="action.php" class="space-y-6">
               <input type="hidden" name="action" value="edit_article">
               <input type="hidden" name="article_id" value="<?php echo $id; ?>">
               
               <div>
                   <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                   <input type="text" name="title" id="title" required
                          value="<?php echo htmlspecialchars($article['title']); ?>"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
               </div>

               <div>
                   <label for="category_id" class="block text-sm font-medium text-gray-700">Catégorie</label>
                   <select name="category_id" id="category_id" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                       <?php foreach($categories as $category): ?>
                           <option value="<?php echo $category['id']; ?>" 
                                   <?php echo $category['id'] == $article['category_id'] ? 'selected' : ''; ?>>
                               <?php echo htmlspecialchars($category['name']); ?>
                           </option>
                       <?php endforeach; ?>
                   </select>
               </div>

               <div>
                   <label for="content" class="block text-sm font-medium text-gray-700">Contenu</label>
                   <textarea name="content" id="content" rows="10" required
                             class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                       <?php echo htmlspecialchars($article['content']); ?>
                   </textarea>
               </div>

               <div class="flex justify-end space-x-4">
                   <a href="pages/articles.php" 
                      class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                       Annuler
                   </a>
                   <button type="submit"
                           class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                       Mettre à jour
                   </button>
               </div>
           </form>
       </div>
   </div>
</body>
</html>