<?php
class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function create($name, $description = null) {
        try {
            $sql = "INSERT INTO categories (name, description) VALUES (:name, :description)";
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch(PDOException $e) {
            error_log("Error creating category: " . $e->getMessage());
            return false;
        }
    }

    public function getAll() {
        try {
            $sql = "SELECT c.*, COUNT(a.id) as article_count 
                    FROM categories c 
                    LEFT JOIN articles a ON c.id = a.category_id 
                    GROUP BY c.id 
                    ORDER BY c.name";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error getting categories: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT * FROM categories WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Error getting category: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $name, $description = null) {
        try {
            $sql = "UPDATE categories SET name = :name, description = :description 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $sql = "DELETE FROM categories WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }

    public function getArticleCount($id) {
        try {
            $sql = "SELECT COUNT(*) FROM articles WHERE category_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch(PDOException $e) {
            error_log("Error counting articles: " . $e->getMessage());
            return 0;
        }
    }
}