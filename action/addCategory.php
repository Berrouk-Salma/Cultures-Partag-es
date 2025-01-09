<?php 
// Start session if not already started 
session_start();

// Include database connection 
require_once '../config/db.php';

$host = "localhost"; // Nom du serveur
$db_name = "art_culture_db"; // Nom de votre base de données
$username = "root"; // Votre nom d'utilisateur MySQL
$password = ""; // Votre mot de passe MySQL

// Créer une connexion
$conn = mysqli_connect($host, $username, $password, $db_name);


$database = new Database();
// $conn = $database->getConnection();
// Check if the form was submitted 
if(isset($_POST['submitCategory'])) {
    // Get and sanitize form data 
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Add debugging to see the received values
    error_log("Received category name: " . $name);
    error_log("Received description: " . $description);

    // Validate input 
    $errors = [];
    
    // Check if name is empty 
    if(empty($name)) {
        $errors[] = "Le nom de la catégorie est requis";
    }

    // Check if category name already exists 
    $check_query = "SELECT id FROM categories WHERE name = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    
    // Add error checking for prepare statement
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($conn));
        $_SESSION['error'] = "Erreur de préparation de la requête";
        header("Location: ../views/admin/categories.php");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $name);
    
    // Add error checking for execute
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
        $_SESSION['error'] = "Erreur d'exécution de la requête";
        header("Location: ../views/admin/categories.php");
        exit();
    }

    mysqli_stmt_store_result($stmt);
    
    if(mysqli_stmt_num_rows($stmt) > 0) {
        $errors[] = "Cette catégorie existe déjà";
    }
    mysqli_stmt_close($stmt);

    // If no errors, proceed with insertion 
    if(empty($errors)) {
        // Prepare INSERT statement 
        $query = "INSERT INTO categories (name, description,user_id) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        
        // Add error checking for prepare
        if (!$stmt) {
            error_log("Insert prepare failed: " . mysqli_error($conn));
            $_SESSION['error'] = "Erreur lors de la préparation de l'insertion";
            header("Location: ../views/admin/categories.php");
            exit();
        }
        $id_admin = (int)$_SESSION['id_user'];

        mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $id_admin);

        // Add error checking for the insertion execution
        if(mysqli_stmt_execute($stmt)) {
            // Log successful insertion
            error_log("Category inserted successfully. ID: " . mysqli_insert_id($conn));
            // Success 
            $_SESSION['success'] = "La catégorie a été ajoutée avec succès";
            mysqli_stmt_close($stmt);
            header("Location: ../views/admin/categories.php");
            exit();
        } else {
            // Log the error details
            error_log("Insert failed: " . mysqli_stmt_error($stmt));
            // Database error 
            $_SESSION['error'] = "Une erreur est survenue lors de l'ajout de la catégorie";
            mysqli_stmt_close($stmt);
            header("Location: ../views/admin/categories.php");
            exit();
        }
    } else {
        // Store errors in session and redirect back 
        $_SESSION['errors'] = $errors;
        header("Location: ../views/admin/categories.php");
        exit();
    }
} else {
    // If accessed directly without form submission 
    header("Location: ../views/admin/categories.php");
    exit();
}

// Close database connection 
mysqli_close($conn);
?>