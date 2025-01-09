<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <nav class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <span class="font-bold text-xl">Admin Panel</span>
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="/culture/views/admin/dashboard.php" 
                           class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Dashboard</a>
                        <a href="/culture/views/admin/categories.php" 
                           class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Categories</a>
                        <a href="/culture/views/admin/tags.php" 
                           class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Tags</a>
                        <a href="/culture/views/admin/authors.php" 
                           class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Authors</a>
                        <a href="/culture/views/admin/pending-articles.php" 
                           class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Pending Articles</a>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-300 text-sm mr-4">
                        <?php 
                        if(isset($_SESSION['name'])) {
                            echo htmlspecialchars($_SESSION['name']);
                        }
                        ?>
                    </span>
                    <a href="/culture/action/logout.php" 
                       class="bg-red-600 hover:bg-red-700 px-3 py-2 rounded-md text-sm font-medium">
                        Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>