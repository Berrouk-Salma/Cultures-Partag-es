<?php
session_start();
include_once '../config/db.php';
include_once "../classes/user.php";

if(isset($_POST['login']) && $_SERVER['REQUEST_METHOD']==='POST') {
    $emailInput = trim($_POST['email']);
    $pwd = $_POST['password'];

    if (!$emailInput || !$pwd) {
        die("Veuillez remplir tous les champs.");
    }
    
    $utilisateur = new User();
    $connexion = $utilisateur->login($emailInput, $pwd);
    
    if (!$connexion) {
        die("Identifiants incorrects.");
    }
    
    $_SESSION['id_user'] = $connexion->getId();
    $_SESSION['prenom'] = $connexion->getPrenom();
    $_SESSION['nom'] = $connexion->getNom();
    $_SESSION['email'] = $connexion->getEmail();
    //$_SESSION['phone'] = $connexion->getTelephone();
    $_SESSION['role'] = $connexion->getRole();
    echo($_SESSION['role']);
    $dashboard = [
        'admin' => '../views/admin/dashboard.php',
        'author' => '../views/author/dashboard.php',
        'user' => '../index.php'
    ];

    // if (isset($dashboard[$_SESSION['role']])) {
        header("Location: " . $dashboard[$_SESSION['role']]);
        exit;
    // }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ArtCulture</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8 p-8 bg-white shadow-lg rounded-lg">
            <div>
                <h2 class="text-center text-3xl font-bold text-gray-900">Connexion</h2>
            </div>
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form class="mt-8 space-y-6" method="POST" >
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                        <input type="password" name="password" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <button type="submit" name="login"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Se connecter
                    </button>
                </div>
            </form>
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">
                    Pas encore de compte? 
                    <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                        S'inscrire
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>