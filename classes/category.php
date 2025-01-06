<?php

require_once __DIR__ . '/../config/db.php';

class Category {
    
    private int $id_categorie;
    private string $nom;
    private string $description;
    private string $date_creation;
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // GETTERS avec return types
    public function getId(): int {
        return $this->id_categorie;
    }

    public function getName(): string {
        return $this->nom;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getDate(): string {
        return $this->date_creation;
    }

    // SETTERS avec void return type
    public function setId(int $id): void {
        $this->id_categorie = $id;
    }

    public function setName(string $nom): void {
        $this->nom = $nom;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setDate(string $date): void {
        $this->date_creation = $date;
    }

    // Récupérer toutes les catégories
    public function getAll(): array {
        try {
            $query = "SELECT C.*, COUNT(A.id_article) as nbr_articles 
                     FROM categorie C
                     LEFT JOIN article A ON C.id_categorie = A.id_categorie
                     GROUP BY C.id_categorie
                     ORDER BY C.nom_categorie ASC";
                     
            $stmt = $this->database->getConnection()->prepare($query);
            $stmt->execute();
            
            return ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;
            
        } catch(PDOException $e) {
            $this->logError('allCategories', $e->getMessage());
            return [];
        }
    }

    // Récupérer une catégorie
    public function showCategorie(int $id): array|false {
        try {
            $query = "SELECT * 
                     FROM categorie 
                     WHERE id_categorie = :id";
                     
            $stmt = $this->database->getConnection()->prepare($query);
            $stmt->execute(['id' => $id]);
            
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
            
        } catch(PDOException $e) {
            $this->logError('showCategorie', $e->getMessage());
            return false;
        }
    }

    // Distribution des articles par catégorie
    public function distributeCategories(int $id_auteur): array|false {
        try {
            $query = "SELECT 
                        C.nom_categorie,
                        COUNT(A.id_article) AS nbr_articles,
                        MAX(A.date_publication) as dernier_article
                     FROM article A 
                     JOIN categorie C ON A.id_categorie = C.id_categorie 
                     WHERE A.id_auteur = :id_auteur
                     GROUP BY C.nom_categorie 
                     ORDER BY nbr_articles DESC";
                     
            $stmt = $this->database->getConnection()->prepare($query);
            $stmt->execute(['id_auteur' => $id_auteur]);
            
            return ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;
            
        } catch(PDOException $e) {
            $this->logError('distributeCategories', $e->getMessage());
            return false;
        }
    }

    // Helper method pour logging
    private function logError(string $method, string $message): void {
        error_log("Categorie::{$method} Error: {$message}");
    }

    // Vérifier si une catégorie existe
    public function exists(int $id): bool {
        try {
            $query = "SELECT COUNT(*) FROM categorie WHERE id_categorie = :id";
            $stmt = $this->database->getConnection()->prepare($query);
            $stmt->execute(['id' => $id]);
            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            $this->logError('exists', $e->getMessage());
            return false;
        }
    }
}