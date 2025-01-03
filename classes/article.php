<?php
class Article {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function create($title, $content, $user_id, $category_id) {
        try {
            $sql = "INSERT INTO articles (title, content, user_id, category_id) 
                    VALUES (:title, :content, :user_id, :category_id)";
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':category_id', $category_id);
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch(PDOException $e) {
            error_log("Error creating article: " . $e->getMessage());
            return false;
        }
    }

    public function getAll($limit = null, $category_id = null) {
        try {
            $sql = "SELECT a.*, c.name as category_name, u.name as author_name 
                    FROM articles a 
                    LEFT JOIN categories c ON a.category_id = c.id 
                    LEFT JOIN users u ON a.user_id = u.id 
                    WHERE a.is_published = 1";
            
            if ($category_id) {
                $sql .= " AND a.category_id = :category_id";
            }
            
            $sql .= " ORDER BY a.published_at DESC";
            
            if ($limit) {
                $sql .= " LIMIT :limit";
            }
            
            $stmt = $this->db->prepare($sql);
            
            if ($category_id) {
                $stmt->bindParam(':category_id', $category_id);
            }
            if ($limit) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error getting articles: " . $e->getMessage());
            return [];
        }
    }

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

    public function publish($id, $admin_id) {
        try {
            $sql = "UPDATE articles SET is_published = 1, published_at = CURRENT_TIMESTAMP 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error publishing article: " . $e->getMessage());
            return false;
        }
    }

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