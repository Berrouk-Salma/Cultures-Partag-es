<?php
require_once "../classes/auteur.php";

$id = $_GET['id'];
$author = new Auteur();
$author->addArticle($id);
header("location: ../pages/articles.php");
?>