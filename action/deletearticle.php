<?php
// action/deletearticle.php
session_start();
require_once '../config/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID de l'article non spécifié";
    header("Location: ../views/author/dashboard.php");
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $articleId = (int)$_GET['id'];
    $userId = $_SESSION['id_user'];

    // First verify if article exists and belongs to the user
    $checkStmt = $conn->prepare("SELECT id FROM articles WHERE id = :id AND user_id = :user_id");
    $checkStmt->bindParam(':id', $articleId, PDO::PARAM_INT);
    $checkStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $checkStmt->execute();

    if ($checkStmt->rowCount() === 0) {
        $_SESSION['error'] = "Article non trouvé ou accès non autorisé";
        header("Location: ../views/author/dashboard.php");
        exit();
    }

    // Delete the article
    $deleteStmt = $conn->prepare("DELETE FROM articles WHERE id = :id ");
    $deleteStmt->bindParam(':id', $articleId, PDO::PARAM_INT);

    if ($deleteStmt->execute()) {
        $_SESSION['success'] = "Article supprimé avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression de l'article";
    }

} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur: " . $e->getMessage();
}

// Redirect back to dashboard
header("Location: ../views/author/dashboard.php");
exit();
?>