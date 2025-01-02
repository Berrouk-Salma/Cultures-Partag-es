<?php

session_start();
require_once 'classes/Database.php';


$page = $_GET['page'] ?? 'home';
$allowed_pages = ['articles', 'users', 'categories'];

if (in_array($page, $allowed_pages)) {
    require_once "pages/{$page}.php";
} else {
    require_once "pages/home.php";
}
?>








