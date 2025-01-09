<?php
// action/getArticle.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_GET['id'])) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $sql = "SELECT a.*, u.prenom, u.nom, c.name as category_name 
                FROM articles a 
                JOIN users u ON a.user_id = u.id 
                JOIN categories c ON a.category_id = c.id 
                WHERE a.id = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        
        if ($article = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $article['author_name'] = $article['prenom'] . ' ' . $article['nom'];
            $article['created_at'] = date('d/m/Y H:i', strtotime($article['created_at']));
            echo json_encode($article);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Article not found']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>