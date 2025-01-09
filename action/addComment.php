<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Check if it's a POST request with required data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['article_id']) && isset($_POST['content'])) {
    try {
        $content = trim($_POST['content']);
        
        if (empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Le contenu du commentaire est requis']);
            exit();
        }

        $database = new Database();
        $conn = $database->getConnection();

        // Insert the comment
        $query = "INSERT INTO article_comments (article_id, user_id, content) 
                 VALUES (:article_id, :user_id, :content)";
        $stmt = $conn->prepare($query);
        
        $result = $stmt->execute([
            'article_id' => $_POST['article_id'],
            'user_id' => $_SESSION['id_user'],
            'content' => $content
        ]);

        if ($result) {
            // Get the newly created comment with user info
            $commentQuery = "SELECT 
                            ac.*,
                            u.name as user_name
                           FROM article_comments ac
                           JOIN users u ON ac.user_id = u.id
                           WHERE ac.id = :comment_id";
            
            $commentStmt = $conn->prepare($commentQuery);
            $commentStmt->execute(['comment_id' => $conn->lastInsertId()]);
            $comment = $commentStmt->fetch();

            echo json_encode([
                'success' => true,
                'message' => 'Commentaire ajouté avec succès',
                'comment' => [
                    'id' => $comment['id'],
                    'content' => $comment['content'],
                    'user_name' => $comment['user_name'],
                    'created_at' => date('d/m/Y H:i', strtotime($comment['created_at']))
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du commentaire']);
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
}
?>