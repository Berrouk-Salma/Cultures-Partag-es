<?php
class Category {
    private $db;
    private $table = 'categories';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($name) {
        $query = "INSERT INTO {$this->table} (name) VALUES (:name)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['name' => $name]);
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArticles($category_id) {
        $query = "SELECT a.* FROM articles a 
                 JOIN {$this->table} c ON a.category_id = c.id 
                 WHERE c.id = :category_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['category_id' => $category_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}