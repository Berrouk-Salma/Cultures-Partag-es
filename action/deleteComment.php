<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'])) {
    try {
        $database = new Database();
        $conn = $database->getConnection();

        // Verify comment belongs to user
        $checkStmt = $conn->prepare(
            "SELECT article_id FROM article_comments 
             WHERE id = :comment_id AND user_id = :user_id"
        );
        $checkStmt->execute([
            'comment_id' => $_POST['comment_id'],
            'user_id' => $_SESSION['id_user']
        ]);
        
        if ($checkStmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM article_comments WHERE id = :comment_id");
        $success = $stmt->execute(['comment_id' => $_POST['comment_id']]);

        echo json_encode(['success' => $success]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>