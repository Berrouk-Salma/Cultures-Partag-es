<?php
session_start();
require_once '../config/db.php';
require_once '../classes/article.php';

// Check admin role
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit();
}

// Assume you fetch the articleId from query parameters or request (for example via GET)
$articleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$article = new Article();
$result = $article->approveArticle($articleId); // Call the approve method

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Article approuvé avec succès']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'approbation de l\'article']);
}