<?php
// action/updateArticle.php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateArticle'])) {
    try {
        $database = new Database();
        $conn = $database->getConnection();

        // Sanitize inputs
        $articleId = (int)$_POST['article_id'];
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

        // Verify article belongs to user
        $checkStmt = $conn->prepare("SELECT id FROM articles WHERE id = ? AND user_id = ?");
        $checkStmt->execute([$articleId, $_SESSION['id_user']]);
        if ($checkStmt->rowCount() === 0) {
            $_SESSION['error'] = "Article non trouvé ou accès non autorisé.";
            header("Location: ../views/auteur/dashboard.php");
            exit();
        }

        // Update article
        $sql = "UPDATE articles 
                SET title = :title, 
                    content = :content, 
                    category_id = :category_id, 
                    is_published = :is_published,
                    updated_at = NOW()
                WHERE id = :id AND user_id = :user_id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':is_published', $is_published, PDO::PARAM_INT);
        $stmt->bindParam(':id', $articleId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['id_user'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Article mis à jour avec succès!";
            header("Location: ../views/author/dashboard.php");
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour de l'article.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur: " . $e->getMessage();
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
    exit();
}
?>