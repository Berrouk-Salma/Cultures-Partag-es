<?php
// classes/Article.php
class Article {
    private $db;
    private $table = 'articles';
    
    public $id;
    public $title;
    public $content;
    public $user_id;
    public $category_id;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (title, content, user_id, category_id) 
                 VALUES (:title, :content, :user_id, :category_id)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                 SET title = :title, content = :content, category_id = :category_id 
                 WHERE id = :id";
        
        $data['id'] = $id;
        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
}