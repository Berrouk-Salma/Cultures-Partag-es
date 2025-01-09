<?php
// action/addArticle.php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addArticle'])) {
    try {
        $database = new Database();
        $conn = $database->getConnection();

        // Sanitize inputs
        $title = trim(htmlspecialchars($_POST['title']));
        $content = trim(htmlspecialchars($_POST['content']));
        $category_id = (int)$_POST['category_id'];
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        
        // Validate inputs
        if (empty($title) || empty($content) || empty($category_id)) {
            $_SESSION['error'] = "Tous les champs sont requis.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Check if category exists and belongs to user
        $checkStmt = $conn->prepare("SELECT id FROM categories WHERE id = ?");
        $checkStmt->execute([$category_id]);
        if ($checkStmt->rowCount() === 0) {
            $_SESSION['error'] = "Catégorie invalide.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Insert new article
        $sql = "INSERT INTO articles (title, content, user_id, category_id, is_published, created_at, updated_at) 
                VALUES (:title, :content, :user_id, :category_id, :is_published, NOW(), NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $_SESSION['id_user'], PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':is_published', $is_published, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Article ajouté avec succès!";
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout de l'article.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur: " . $e->getMessage();
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>