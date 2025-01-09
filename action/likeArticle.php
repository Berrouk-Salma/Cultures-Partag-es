<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['article_id'])) {
    try {
        $database = new Database();
        $conn = $database->getConnection();

        // Check if user already liked the article
        $checkStmt = $conn->prepare(
            "SELECT id FROM article_likes 
             WHERE article_id = :article_id AND user_id = :user_id"
        );
        $checkStmt->execute([
            'article_id' => $_POST['article_id'],
            'user_id' => $_SESSION['id_user']
        ]);

        if ($checkStmt->rowCount() > 0) {
            // Unlike
            $stmt = $conn->prepare(
                "DELETE FROM article_likes 
                 WHERE article_id = :article_id AND user_id = :user_id"
            );
            $liked = false;
        } else {
            // Like
            $stmt = $conn->prepare(
                "INSERT INTO article_likes (article_id, user_id) 
                 VALUES (:article_id, :user_id)"
            );
            $liked = true;
        }

        $stmt->execute([
            'article_id' => $_POST['article_id'],
            'user_id' => $_SESSION['id_user']
        ]);

        // Get updated like count
        $countStmt = $conn->prepare(
            "SELECT COUNT(*) as count FROM article_likes WHERE article_id = ?"
        );
        $countStmt->execute([$_POST['article_id']]);
        $likeCount = $countStmt->fetch()['count'];

        echo json_encode([
            'success' => true,
            'likes' => $likeCount,
            'liked' => $liked
        ]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}