<?php
/**
 * Delete Budget Handler
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::requireLogin();

$budgetModel = new Budget();
$userId = Session::getUserId();

$budget_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($budget_id <= 0) {
    header('Location: budgets.php?error=Invalid budget');
    exit();
}

try {
    if (!$budgetModel->belongsToUser($budget_id, $userId)) {
        header('Location: budgets.php?error=Budget not found');
        exit();
    }
    
    $budgetModel->delete($budget_id, $userId);
    header('Location: budgets.php?success=deleted');
    exit();
    
} catch (PDOException $e) {
    header('Location: budgets.php?error=An error occurred. Please try again.');
    exit();
}
