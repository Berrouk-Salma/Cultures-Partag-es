<?php

require_once __DIR__ . '/../config/db.php';

class Article {
    private const STATUS_ACCEPTED = 'Accepté';
    private const STATUS_REFUSED = 'Refusé';
    private const STATUS_PENDING = 'En Attente';
    
    private $database;
    private int $id;
    private string $title;
    private string $content;
    private string $date_pub;

    
    public function __construct() {
        $this->database = new Database();
    }
    
    public function getArticlesByStatus(string $status) {
        try {
            $sql = "SELECT * , C.name AS category_name , A.created_at AS date_pub, A.id AS id_article
                    FROM categories C 
                    JOIN articles A ON A.category_id = C.id
                    JOIN users U ON U.id = A.user_id 
                    WHERE A.etat = :status";
                    
            $stmt = $this->database->getConnection()->prepare($sql);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();


            
            // return ($stmt->rowCount() > 0) ? 
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC) ;
            // : [];

            return $result ;
            
        } catch(PDOException $e) {
            $this->logError('getArticlesByStatus', $e->getMessage());
            return [];
        }
    }
    
    public function getAll() {
        return $this->getArticlesByStatus(self::STATUS_ACCEPTED);
    }
    
    public function refusedArticles() {
        return $this->getArticlesByStatus(self::STATUS_REFUSED);
    }
    
    public function pendingArticles() {
        return $this->getArticlesByStatus(self::STATUS_PENDING);
    }
    
    public function showArticle(int $id) {
        try {
            $sql = "SELECT * 
                    FROM article A 
                    JOIN users U ON A.id_auteur = U.id_user 
                    JOIN categorie C ON A.id_categorie = C.id_categorie 
                    WHERE A.id_article = :id AND A.etat = :status";
                    
            $stmt = $this->database->getConnection()->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'status' => self::STATUS_ACCEPTED
            ]);
            
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : [];
            
        } catch(PDOException $e) {
            $this->logError('showArticle', $e->getMessage());
            return [];
        }
    }
    
    private function countArticlesQuery(string $query, array $params) {
        try {
            $stmt = $this->database->getConnection()->prepare($query);
            $stmt->execute($params);
            
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC)['nbr_articles'] : 0;
            
        } catch(PDOException $e) {
            $this->logError('countArticles', $e->getMessage());
            return 0;
        }
    }
    
    public function countArticles(int $id_auteur) {
        $query = "SELECT COUNT(id_article) AS nbr_articles 
                 FROM article 
                 WHERE id_auteur = :id";
                 
        return $this->countArticlesQuery($query, ['id' => $id_auteur]);
    }
    
    public function statusArticles(int $id_auteur, string $etat) {
        $query = "SELECT COUNT(id_article) AS nbr_articles 
                 FROM article 
                 WHERE id_auteur = :id AND etat = :status";
                 
        return $this->countArticlesQuery($query, [
            'id' => $id_auteur,
            'status' => $etat
        ]);
    }
    
    private function logError(string $method, string $message) {
        error_log("Article::{$method} Error: {$message}");
    }
    
    // Standard getters/setters
    public function getId(): int {
        return $this->id;
    }
    
    public function getTitle(): string {
        return $this->title;
    }
    
    public function getContent(): string {
        return $this->content;
    }
    
    public function getDate(): string {
        return $this->date_pub;
    }
    
    public function setId(int $id): void {
        $this->id = $id;
    }
    
    public function setTitle(string $title): void {
        $this->title = $title;
    }
    
    public function setContent(string $content): void {
        $this->content = $content;
    }
    
    public function setDate(string $date): void {
        $this->date_pub = $date;
    }

    public function getArticlesByAuthor($authorId) {
        try {
            $sql = "SELECT articles.*, categories.name as category_name 
                   FROM articles
                   LEFT JOIN categories ON articles.category_id = categories.id 
                   WHERE articles.user_id = :author_id 
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
    
    public function ShowAllArticles() {
        try {
            $sql = "SELECT 
                        a.id AS article_id,
                        a.title AS article_title,
                        a.content AS article_content,
                        a.created_at AS article_created_at,
                        a.updated_at AS article_updated_at,
                        a.is_published AS article_status,
                        a.published_at AS article_published_at,
                        u.name AS author_name, 
                        c.name AS category_name,
                        COUNT(l.id) AS total_likes
                    FROM 
                        articles a
                    INNER JOIN 
                        users u ON a.user_id = u.id 
                    INNER JOIN 
                        categories c ON a.category_id = c.id 
                    LEFT JOIN 
                        likes l ON l.article_id = a.id
                    WHERE 
                        a.is_published = TRUE
                    GROUP BY 
                        a.id, u.name, c.name
                    ORDER BY 
                        a.published_at DESC";
    
            $stmt = $this->database->getConnection()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Debug the result
            if ($result) {
                error_log(print_r($result, true)); // Log the result to check what's returned
            } else {
                error_log("No articles found or query failed.");
            }
    
            return $result ?: []; // Return empty array if no result is found
    
        } catch (PDOException $e) {
            $this->logError('ShowAllArticles', $e->getMessage());
            return [];
        }
    }
    
    
}
