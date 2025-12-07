<?php
/**
 * Delete Category Handler
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::requireLogin();

$categoryModel = new Category();
$userId = Session::getUserId();

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($category_id <= 0) {
    header('Location: categories.php?error=Invalid category');
    exit();
}

try {
    if (!$categoryModel->belongsToUser($category_id, $userId)) {
        header('Location: categories.php?error=Category not found');
        exit();
    }
    
    $categoryModel->delete($category_id, $userId);
    header('Location: categories.php?success=deleted');
    exit();
    
} catch (PDOException $e) {
    header('Location: categories.php?error=An error occurred. Please try again.');
    exit();
}
