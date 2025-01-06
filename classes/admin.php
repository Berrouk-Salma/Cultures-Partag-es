<?php

require_once __DIR__ .'./user.php';

class Admin extends User {
    private const ARTICLE_ACCEPTED = 'Accepté';
    private const ARTICLE_REFUSED = 'Refusé';

    public function showCategories() {
        try {
            $sql = "SELECT C.*, COUNT(A.id_article) AS nbr_articles 
                    FROM categorie C 
                    LEFT JOIN article A ON C.id_categorie = A.id_categorie 
                    GROUP BY C.id_categorie";
            
            $stmt = $this->database->getConnection()->prepare($sql);
            $stmt->execute();
            
            return ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            
        } catch (PDOException $e) {
            $this->logError('showCategories', $e->getMessage());
            return [];
        }
    }

    public function addCategorie(int $id_admin, string $nom, string $description) {
        try {
            $this->validateCategorie($nom, $description);

            $sql = "INSERT INTO categorie (id_admin, nom_categorie, description) 
                    VALUES (:id_admin, :nom, :description)";
                    
            $stmt = $this->database->getConnection()->prepare($sql);
            $success = $stmt->execute([
                'id_admin' => $id_admin,
                'nom' => $nom,
                'description' => $description
            ]);

            if($success) {
                header("location: ../views/admin/dashboard.php");
                exit;
            }
            throw new Exception("Erreur lors de l'ajout");
            
        } catch (PDOException $e) {
            $this->logError('addCategorie', $e->getMessage());
            return false;
        }
    }

    public function editCategorie(int $id, string $nom, string $description) {
        try {
            $this->validateCategorie($nom, $description);
            
            if(!$this->categorieExists($id)) {
                return false;
            }

            $sql = "UPDATE categorie 
                    SET nom_categorie = :nom, description = :description 
                    WHERE id_categorie = :id";
                    
            $stmt = $this->database->getConnection()->prepare($sql);
            $success = $stmt->execute([
                'id' => $id,
                'nom' => $nom,
                'description' => $description
            ]);

            if($success) {
                header("location: ../views/admin/dashboard.php");
                exit;
            }
            return false;
            
        } catch (PDOException $e) {
            $this->logError('editCategorie', $e->getMessage());
            return false;
        }
    }

    public function deleteCategorie(int $id) {
        try {
            if(!$this->categorieExists($id)) {
                return false;
            }

            $sql = "DELETE FROM categorie WHERE id_categorie = :id";
            $stmt = $this->database->getConnection()->prepare($sql);
            $success = $stmt->execute(['id' => $id]);

            if($success) {
                header("location: ../views/admin/dashboard.php");
                exit;
            }
            return false;
            
        } catch (PDOException $e) {
            $this->logError('deleteCategorie', $e->getMessage());
            return false;
        }
    }

    public function updateArticleStatus(int $id_article, string $new_status) {
        try {
            if(!$this->articleExists($id_article)) {
                return false;
            }

            $sql = "UPDATE article SET etat = :status WHERE id_article = :id";
            $stmt = $this->database->getConnection()->prepare($sql);
            $success = $stmt->execute([
                'id' => $id_article,
                'status' => $new_status
            ]);

            if($success) {
                header("location: ../views/admin/dashboard.php");
                exit;
            }
            return false;
            
        } catch (PDOException $e) {
            $this->logError('updateArticleStatus', $e->getMessage());
            return false;
        }
    }

    public function approveArticle(int $id_article) {
        return $this->updateArticleStatus($id_article, self::ARTICLE_ACCEPTED);
    }

    public function rejectArticle(int $id_article) {
        return $this->updateArticleStatus($id_article, self::ARTICLE_REFUSED);
    }

    private function validateCategorie(string $nom, string $description) {
        if(empty(trim($nom)) || strlen($nom) > 50) {
            throw new Exception("Nom de catégorie invalide");
        }
        if(empty(trim($description))) {
            throw new Exception("Description requise");
        }
    }

    private function categorieExists(int $id): bool {
        $sql = "SELECT COUNT(*) FROM categorie WHERE id_categorie = :id";
        $stmt = $this->database->getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn() > 0;
    }

    private function articleExists(int $id): bool {
        $sql = "SELECT COUNT(*) FROM article WHERE id_article = :id";
        $stmt = $this->database->getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn() > 0;
    }

    private function logError(string $method, string $error) {
        error_log("Admin::{$method} Error: " . $error);
    }
}