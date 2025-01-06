<?php
// action/addcategory.php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCategory'])) {
    try {
        $database = new Database();
        $conn = $database->getConnection();

        // Sanitize inputs
        $name = trim(htmlspecialchars($_POST['name']));
        $description = trim(htmlspecialchars($_POST['description']));

        // Validate inputs
        if (empty($name) || empty($description)) {
            $_SESSION['error'] = "Tous les champs sont requis.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Check if category already exists
        $checkStmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $checkStmt->execute([$name]);
        if ($checkStmt->rowCount() > 0) {
            $_SESSION['error'] = "Cette catégorie existe déjà.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Insert new category
        $sql = "INSERT INTO categories (name, description, user_id) VALUES (:name, :description, :user_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $_SESSION['id_user'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Catégorie ajoutée avec succès!";
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout de la catégorie.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur: " . $e->getMessage();
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>