<?php
class Article {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Créer un article
    public function create($title, $content, $user_id, $category_id) {
        try {
            $sql = "INSERT INTO articles (title, content, user_id, category_id) 
                    VALUES (:title, :content, :user_id, :category_id)";
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':category_id', $category_id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error creating article: " . $e->getMessage());
            return false;
        }
    }

    // Récupérer tous les articles
    public function getAll() {
        try {
            $sql = "SELECT a.*, c.name as category_name, u.name as author_name 
                    FROM articles a 
                    LEFT JOIN categories c ON a.category_id = c.id 
                    LEFT JOIN users u ON a.user_id = u.id 
                    ORDER BY a.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error getting articles: " . $e->getMessage());
            return [];
        }
    }

    // Récupérer un article par ID
    public function getById($id) {
        try {
            $sql = "SELECT a.*, c.name as category_name, u.name as author_name 
                    FROM articles a 
                    LEFT JOIN categories c ON a.category_id = c.id 
                    LEFT JOIN users u ON a.user_id = u.id 
                    WHERE a.id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Error getting article: " . $e->getMessage());
            return false;
        }
    }

    // Modifier un article
    public function update($id, $data, $user_id) {
        try {
            $sets = [];
            $params = [':id' => $id, ':user_id' => $user_id];

            foreach($data as $key => $value) {
                if($key !== 'id' && $key !== 'user_id') {
                    $sets[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            $sql = "UPDATE articles SET " . implode(', ', $sets) . 
                   " WHERE id = :id AND user_id = :user_id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch(PDOException $e) {
            error_log("Error updating article: " . $e->getMessage());
            return false;
        }
    }

    // Supprimer un article
    public function delete($id, $user_id) {
        try {
            $sql = "DELETE FROM articles WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error deleting article: " . $e->getMessage());
            return false;
        }
    }

    // Approuver un article
    public function approve($article_id) {
        try {
            $sql = "UPDATE articles SET is_published = 1, published_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $article_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error approving article: " . $e->getMessage());
            return false;
        }
    }

    // Rejeter un article
    public function reject($article_id) {
        try {
            $sql = "UPDATE articles SET is_published = 0 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $article_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error rejecting article: " . $e->getMessage());
            return false;
        }
    }

    // Articles en attente
    public function getPendingArticles() {
        try {
            $sql = "SELECT a.*, c.name as category_name, u.name as author_name 
                    FROM articles a 
                    LEFT JOIN categories c ON a.category_id = c.id 
                    LEFT JOIN users u ON a.user_id = u.id 
                    WHERE a.is_published = 0 
                    ORDER BY a.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error getting pending articles: " . $e->getMessage());
            return [];
        }
    }

    // Articles d'un utilisateur
    public function getUserArticles($user_id) {
        try {
            $sql = "SELECT a.*, c.name as category_name 
                    FROM articles a 
                    LEFT JOIN categories c ON a.category_id = c.id 
                    WHERE a.user_id = :user_id 
                    ORDER BY a.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error getting user articles: " . $e->getMessage());
            return [];
        }
    }
}