<?php
session_start();
include_once '../classes/user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['signup'])) {
        die("Accès non autorisé.");
    }

    $userData = [
        'nom' => $_POST['nom'] ?? '',
        'prenom' => $_POST['prenom'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
        'role' => $_POST['role'] ?? '',
        'photo_url' => null
    ];

    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $newName = uniqid() . '.' . $filetype;
            $uploadPath = '../uploads/profiles/' . $newName;

            // Create directory if it doesn't exist
            if (!file_exists('../uploads/profiles/')) {
                mkdir('../uploads/profiles/', 0777, true);
            }

            // Move uploaded file
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                $userData['photo_url'] = $newName;
            }
        }
    }

    $newUser = new User();
    $inscription = $newUser->register(
        $userData['nom'],
        $userData['prenom'], 
        $userData['email'],
        $userData['password'],
        $userData['role'],
        $userData['photo_url']
    );

    if (!$inscription) {
        die("Échec de l'inscription.");
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - CultureConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-lg w-full space-y-6 bg-white p-8 rounded-xl shadow-xl">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-800">Créer un compte</h2>
                <p class="mt-2 text-gray-600">Rejoignez notre communauté</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="mt-8 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prénom</label>
                        <input type="text" 
                               name="prenom" 
                               required 
                               class="mt-1 w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" 
                               name="nom" 
                               required 
                               class="mt-1 w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Photo Upload Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Photo de profil</label>
                    <div class="mt-1 flex items-center">
                        <div id="preview" class="hidden w-20 h-20 rounded-full overflow-hidden bg-gray-100 mr-4">
                            <img id="preview-img" src="" alt="Preview" class="w-full h-full object-cover">
                        </div>
                        <label class="cursor-pointer px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <span>Choisir une photo</span>
                            <input type="file" 
                                   name="photo" 
                                   accept="image/*"
                                   class="hidden"
                                   onchange="previewImage(this)">
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">JPG, PNG ou GIF (Max. 2MB)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" 
                           name="email" 
                           required 
                           class="mt-1 w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <input type="password" 
                           name="password" 
                           required 
                           class="mt-1 w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Type de compte</label>
                    <select name="role" 
                            required 
                            class="mt-1 w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="user">Utilisateur</option>
                        <option value="author">Auteur</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" 
                        name="signup"
                        class="mt-6 w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    S'inscrire
                </button>
            </form>

            <p class="text-center text-sm text-gray-600">
                Déjà inscrit? 
                <a href="login.php" class="font-medium text-blue-600 hover:text-blue-500 hover:underline">
                    Se connecter
                </a>
            </p>
        </div>
    </div>

    <script>
    function previewImage(input) {
        const preview = document.getElementById('preview');
        const previewImg = document.getElementById('preview-img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>