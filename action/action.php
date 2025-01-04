<?php
session_start();

require_once 'classes/database.php';
require_once 'classes/article.php';
require_once 'classes/category.php';
require_once 'classes/user.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

// Récupérer l'action demandée
$action = $_POST['action'] ?? '';

// Initialiser les objets
$articleObj = new Article();
$categoryObj = new Category();
$userObj = new User();

switch($action) {
    // Actions pour les articles
    case 'create_article':
        if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'author') {
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $content = $_POST['content'];
            $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
            
            $result = $articleObj->create($title, $content, $_SESSION['user_id'], $category_id);
            if ($result) {
                header('Location: pages/articles.php?success=created');
            } else {
                header('Location: pages/articles.php?error=create_failed');
            }
        }
        break;

    case 'edit_article':
        $article_id = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $content = $_POST['content'];
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        
        $data = [
            'title' => $title,
            'content' => $content,
            'category_id' => $category_id
        ];
        
        $result = $articleObj->update($article_id, $data, $_SESSION['user_id']);
        if ($result) {
            header('Location: pages/articles.php?success=updated');
        } else {
            header('Location: pages/articles.php?error=update_failed');
        }
        break;

    case 'delete_article':
        $article_id = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
        if ($articleObj->delete($article_id, $_SESSION['user_id'])) {
            header('Location: pages/articles.php?success=deleted');
        } else {
            header('Location: pages/articles.php?error=delete_failed');
        }
        break;

    // Actions pour les catégories
    case 'create_category':
        if ($_SESSION['role'] === 'admin') {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            
            if ($categoryObj->create($name, $description)) {
                header('Location: pages/categories.php?success=created');
            } else {
                header('Location: pages/categories.php?error=create_failed');
            }
        }
        break;

    case 'edit_category':
        if ($_SESSION['role'] === 'admin') {
            $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            
            if ($categoryObj->update($category_id, $name, $description)) {
                header('Location: pages/categories.php?success=updated');
            } else {
                header('Location: pages/categories.php?error=update_failed');
            }
        }
        break;

    case 'delete_category':
        if ($_SESSION['role'] === 'admin') {
            $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
            if ($categoryObj->delete($category_id)) {
                header('Location: pages/categories.php?success=deleted');
            } else {
                header('Location: pages/categories.php?error=delete_failed');
            }
        }
        break;

    // Actions pour les utilisateurs (admin seulement)
    case 'update_user':
        if ($_SESSION['role'] === 'admin') {
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
            $data = [
                'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'role' => filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING)
            ];
            
            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }
            
            if ($userObj->updateUser($user_id, $data)) {
                header('Location: pages/users.php?success=updated');
            } else {
                header('Location: pages/users.php?error=update_failed');
            }
        }
        break;

    case 'delete_user':
        if ($_SESSION['role'] === 'admin') {
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
            if ($userObj->deleteUser($user_id)) {
                header('Location: pages/users.php?success=deleted');
            } else {
                header('Location: pages/users.php?error=delete_failed');
            }
        }
        break;

    case 'publish_article':
        if ($_SESSION['role'] === 'admin') {
            $article_id = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
            if ($articleObj->publish($article_id, $_SESSION['user_id'])) {
                header('Location: pages/articles.php?success=published');
            } else {
                header('Location: pages/articles.php?error=publish_failed');
            }
        }
        break;

    default:
        header('Location: index.php');
        break;
}

exit();