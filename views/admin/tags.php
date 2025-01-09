<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Check if user is logged in and is admin
if(!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../views/login.php');
    exit();
}

try {
    // Create database connection
    $database = new Database();
    $conn = $database->getConnection();

    // Fetch all tags
    $query = "SELECT * FROM tags ORDER BY name ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();

} catch(Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Tags - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">


    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Gestion des Tags</h1>
            <button 
                onclick="document.getElementById('tagModal').classList.remove('hidden')"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
            >
                Ajouter un Tag
            </button>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $_SESSION['success']; ?></span>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $_SESSION['error']; ?></span>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Tags Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de création</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($result as $tag): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($tag['id']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($tag['name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('d/m/Y H:i', strtotime($tag['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button 
                                    onclick="editTag(<?php echo $tag['id']; ?>, '<?php echo htmlspecialchars($tag['name']); ?>')"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                >
                                    Modifier
                                </button>
                                <button 
                                    onclick="deleteTag(<?php echo $tag['id']; ?>)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Tag Modal -->
    <div id="tagModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Ajouter un Tag</h3>
                <form action="../../action/addTag.php" method="POST" class="space-y-4">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
        <input type="text" name="name" id="name" required
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div class="flex justify-end space-x-3 mt-6">
        <button type="button"
                onclick="document.getElementById('tagModal').classList.add('hidden')"
                class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
            Annuler
        </button>
        <button type="submit" name="addTag"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
            Ajouter
        </button>
    </div>
</form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Confirmer la suppression</h3>
            <p class="mb-4 text-sm text-gray-500">Êtes-vous sûr de vouloir supprimer ce tag ?</p>
            <form action="../../action/deleteTag.php" method="POST" class="flex justify-end space-x-3">
                <input type="hidden" name="tag_id" id="deleteTagId">
                <button type="button"
                        onclick="document.getElementById('deleteModal').classList.add('hidden')"
                        class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
                    Annuler
                </button>
                <button type="submit" name="deleteTag"
                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <script>
        function deleteTag(tagId) {
            document.getElementById('deleteTagId').value = tagId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function editTag(tagId, tagName) {
            // Implement edit functionality
            console.log('Edit tag:', tagId, tagName);
        }
    </script>
</body>
</html>