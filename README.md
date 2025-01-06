# Cultures-Partag-es
Cultures Partagées a besoin d'un système de gestion de contenu performant et d'un front convivial pour faciliter la création, l'organisation et la découverte d'articles culturels sur la plateforme.


<?php
class Article {
    private $database;
    private $table = "articles";

    public function __construct() {
        $this->database = new Database();
    }

    public function getArticlesByAuthor($authorId) {
        try {
            $sql = "SELECT articles.*, categories.name as category_name 
                   FROM " . $this->table . " 
                   LEFT JOIN categories ON articles.category_id = categories.id 
                   WHERE author_id = :author_id 
                   ORDER BY created_at DESC";

            $stmt = $this->database->getConnection()->prepare($sql);
            $stmt->bindParam(":author_id", $authorId, PDO::PARAM_INT);
            $stmt->execute();

            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // If no articles found, return empty array
            if (!$articles) {
                return [];
            }

            return $articles;

        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la récupération des articles: " . $e->getMessage();
            return false;
        }
    }

    // Optional: Add a method to get article count for an author
    public function getArticleCount($authorId) {
        try {
            $sql = "SELECT COUNT(*) FROM " . $this->table . " WHERE author_id = :author_id";
            $stmt = $this->database->getConnection()->prepare($sql);
            $stmt->bindParam(":author_id", $authorId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchColumn();

        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors du comptage des articles: " . $e->getMessage();
            return 0;
        }
    }

    // Example usage in your dashboard:
    /*
    $article = new Article();
    $articles = $article->getArticlesByAuthor($_SESSION['id_user']);
    $articleCount = $article->getArticleCount($_SESSION['id_user']);
    */
}

// Required SQL table structure:
/*
CREATE TABLE articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
*/