<?php

require_once __DIR__ .'/utilisateur.php';

class Auteur extends Utilisateur {
    private const ETAT_ACCEPTED = 'Accepté';
    private const ROLE_AUTEUR = 'Auteur';

    public function addArticle(string $titre, string $contenu, int $id_auteur, int $id_categorie) {
        try {
            $sql = 'INSERT INTO article (titre, contenu, id_auteur, id_categorie) 
                    VALUES (:titre, :contenu, :id_auteur, :id_categorie)';
                    
            $stmt = $this->database->getConnection()->prepare($sql);
            
            if($stmt->execute([
                'titre' => $titre,
                'contenu' => $contenu,
                'id_auteur' => $id_auteur,
                'id_categorie' => $id_categorie
            ])) {
                header("location: ../views/auteur/dashboard.php");
                exit;
            }
            return false;
        } catch (PDOException $e) {
            $this->logError('addArticle', $e->getMessage());
            return false;
        }
    }

    public function editArticle(int $id_article, string $titre, string $contenu, int $id_categorie) {
        try {
            $sql = "UPDATE article 
                    SET titre = :titre, 
                        contenu = :contenu, 
                        id_categorie = :id_categorie 
                    WHERE id_article = :id_article";
                    
            $stmt = $this->database->getConnection()->prepare($sql);
            
            if($stmt->execute([
                'titre' => $titre,
                'contenu' => $contenu,
                'id_categorie' => $id_categorie,
                'id_article' => $id_article
            ])) {
                header("location: ../views/auteur/dashboard.php");
                exit;
            }
            return false;
        } catch (PDOException $e) {
            $this->logError('editArticle', $e->getMessage());
            return false;
        }
    }

    public function deleteArticle(int $id_article) {
        try {
            $sql = "DELETE FROM article WHERE id_article = :id_article";
            $stmt = $this->database->getConnection()->prepare($sql);
            
            if($stmt->execute(['id_article' => $id_article])) {
                header("location: ../views/auteur/dashboard.php");
                exit;
            }
            return false;
        } catch (PDOException $e) {
            $this->logError('deleteArticle', $e->getMessage());
            return false;
        }
    }

    public function ownArticles(int $id_auteur) {
        return $this->getArticlesByAuteur($id_auteur);
    }

    public function recentArticles(int $id_auteur) {
        return $this->getArticlesByAuteur($id_auteur, 3);
    }

    public function acceptedArticles(int $id_auteur) {
        return $this->getArticlesByAuteur($id_auteur, null, self::ETAT_ACCEPTED);
    }

    private function getArticlesByAuteur(int $id_auteur, ?int $limit = null, ?string $etat = null) {
        try {
            $sql = "SELECT * FROM article A 
                    JOIN categorie C ON A.id_categorie = C.id_categorie
                    JOIN users U ON U.id_user = A.id_auteur 
                    WHERE A.id_auteur = :id";
            
            if($etat) {
                $sql .= " AND A.etat = :etat";
            }
            
            $sql .= " ORDER BY A.date_publication DESC, A.id_article DESC";
            
            if($limit) {
                $sql .= " LIMIT :limit";
            }
            
            $stmt = $this->database->getConnection()->prepare($sql);
            $params = ['id' => $id_auteur];
            
            if($etat) {
                $params['etat'] = $etat;
            }
            
            if($limit) {
                $params['limit'] = $limit;
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            }
            
            $stmt->execute($params);
            
            return ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            
        } catch(PDOException $e) {
            $this->logError('getArticlesByAuteur', $e->getMessage());
            return [];
        }
    }

    public function showAuthors() {
        try {
            $sql = "SELECT U.*, COUNT(A.id_article) as total_articles 
                    FROM users U 
                    LEFT JOIN article A ON U.id_user = A.id_auteur 
                    WHERE U.role = :role 
                    GROUP BY U.id_user";
                    
            $stmt = $this->database->getConnection()->prepare($sql);
            $stmt->execute(['role' => self::ROLE_AUTEUR]);
            
            return ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            
        } catch(PDOException $e) {
            $this->logError('showAuthors', $e->getMessage());
            return [];
        }
    }

    private function logError(string $method, string $message): void {
        error_log("Auteur::{$method} Error: {$message}");
    }
}