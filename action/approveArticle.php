<?php
session_start();
require_once '../config/db.php';

class ArticleManager {
    private PDO $conn;

    public function __construct(PDO $db_connection) {
        $this->conn = $db_connection;
    }

    public function approveArticle(int $articleId): bool {
        try {
            // Begin transaction
            $this->conn->beginTransaction();

            // Update article status
            $query = "UPDATE articles 
                     SET is_published = true, 
                         published_at = CURRENT_TIMESTAMP 
                     WHERE id = :article_id";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute(['article_id' => $articleId]);

            if ($result) {
                // Commit the transaction
                $this->conn->commit();
                return true;
            }

            // Rollback if something went wrong
            $this->conn->rollBack();
            return false;

        } catch (PDOException $e) {
            // Rollback on error
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw new Exception("Erreur lors de l'approbation: " . $e->getMessage());
        }
    }

    public function checkArticleExists(int $articleId): bool {
        $query = "SELECT id FROM articles WHERE id = :article_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['article_id' => $articleId]);
        return $stmt->rowCount() > 0;
    }
}

// Check if user is admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit();
}

// Check if article ID is provided
if (!isset($_POST['article_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de l\'article manquant']);
    exit();
}

try {
    $articleId = (int)$_POST['article_id'];
    
    $database = new Database();
    $articleManager = new ArticleManager($database->getConnection());

    // Check if article exists
    if (!$articleManager->checkArticleExists($articleId)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Article non trouvé']);
        exit();
    }

    // Approve article
    $result = $articleManager->approveArticle($articleId);

    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => 'Article approuvé avec succès'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Erreur lors de l\'approbation de l\'article'
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>