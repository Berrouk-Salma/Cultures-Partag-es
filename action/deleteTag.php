<?php
session_start();
require_once '../config/db.php';

if(isset($_POST['deleteTag']) && isset($_POST['tag_id'])) {
    $tag_id = mysqli_real_escape_string($conn, $_POST['tag_id']);
    
    $query = "DELETE FROM tags WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $tag_id);

    if(mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Le tag a été supprimé avec succès";
    } else {
        $_SESSION['error'] = "Une erreur est survenue lors de la suppression du tag";
    }
    mysqli_stmt_close($stmt);
}

header("Location: ../views/admin/tags.php");
exit();
?>