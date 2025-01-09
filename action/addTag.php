<?php
session_start();
require_once '../config/db.php';

class TagManager {
    private PDO $conn;  // This is line 6

    public function __construct(PDO $db_connection) {
        $this->conn = $db_connection;
    }

    public function addTag(string $name): bool {
        try {
            // Check if tag exists
            $checkStmt = $this->conn->prepare("SELECT id FROM tags WHERE name = ?");
            $checkStmt->execute([$name]);
            
            if ($checkStmt->rowCount() > 0) {
                $_SESSION['error'] = "Ce tag existe déjà";
                return false;
            }

            // Insert tag
            $insertStmt = $this->conn->prepare("INSERT INTO tags (name) VALUES (?)");
            
            if ($insertStmt->execute([$name])) {
                $_SESSION['success'] = "Le tag a été ajouté avec succès";
                return true;
            }

            $_SESSION['error'] = "Erreur lors de l'ajout du tag";
            return false;

        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur: " . $e->getMessage();
            return false;
        }
    }
}

// Handle the form submission
if (isset($_POST['addTag'])) {
    try {
        $tagName = trim($_POST['name'] ?? '');
        
        if (empty($tagName)) {
            $_SESSION['error'] = "Le nom du tag est requis";
            header("Location: ../views/admin/tags.php");
            exit();
        }

        // Create new database connection and tag manager
        $database = new Database();
        $tagManager = new TagManager($database->getConnection());
        $tagManager->addTag($tagName);

    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur: " . $e->getMessage();
    }
    
    header("Location: ../views/admin/tags.php");
    exit();
}

// If accessed directly without form submission
header("Location: ../views/admin/tags.php");
exit();