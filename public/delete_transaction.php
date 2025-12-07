<?php
/**
 * Delete Transaction Handler
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::requireLogin();

$transactionModel = new Transaction();
$userId = Session::getUserId();

$transaction_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($transaction_id <= 0) {
    header('Location: transactions.php?error=Invalid transaction');
    exit();
}

try {
    $transaction = $transactionModel->findByIdAndUser($transaction_id, $userId);
    
    if (!$transaction) {
        header('Location: transactions.php?error=Transaction not found');
        exit();
    }
    
    $transactionModel->delete($transaction_id, $userId);
    header('Location: transactions.php?success=deleted');
    exit();
    
} catch (PDOException $e) {
    header('Location: transactions.php?error=An error occurred. Please try again.');
    exit();
}
