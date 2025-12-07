<?php
/**
 * Transaction Model
 * Handles transaction-related database operations
 */

class Transaction {
    private $pdo;
    
    public function __construct($pdo = null) {
        $this->pdo = $pdo ?? App::db();
    }
    
    public function create($userId, $categoryId, $amount, $date, $description, $type) {
        $stmt = $this->pdo->prepare("INSERT INTO transactions (user_id, category_id, amount, transaction_date, description, type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $categoryId, $amount, $date, $description, $type]);
        return $this->pdo->lastInsertId();
    }
    
    public function findById($transactionId) {
        $stmt = $this->pdo->prepare("SELECT * FROM transactions WHERE transaction_id = ?");
        $stmt->execute([$transactionId]);
        return $stmt->fetch();
    }
    
    public function findByIdAndUser($transactionId, $userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM transactions WHERE transaction_id = ? AND user_id = ?");
        $stmt->execute([$transactionId, $userId]);
        return $stmt->fetch();
    }
    
    public function getAllByUser($userId) {
        $stmt = $this->pdo->prepare("
            SELECT t.*, c.category_name 
            FROM transactions t
            JOIN categories c ON t.category_id = c.category_id
            WHERE t.user_id = ? 
            ORDER BY t.transaction_date DESC, t.transaction_id DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getRecentByUser($userId, $limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT t.*, c.category_name 
            FROM transactions t 
            LEFT JOIN categories c ON t.category_id = c.category_id 
            WHERE t.user_id = ? 
            ORDER BY t.transaction_date DESC, t.transaction_id DESC 
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    public function update($transactionId, $userId, $amount, $categoryId, $date, $description, $type) {
        $stmt = $this->pdo->prepare("UPDATE transactions SET amount = ?, category_id = ?, transaction_date = ?, description = ?, type = ? WHERE transaction_id = ? AND user_id = ?");
        return $stmt->execute([$amount, $categoryId, $date, $description, $type, $transactionId, $userId]);
    }
    
    public function delete($transactionId, $userId) {
        $stmt = $this->pdo->prepare("DELETE FROM transactions WHERE transaction_id = ? AND user_id = ?");
        return $stmt->execute([$transactionId, $userId]);
    }
    
    public function getTotalIncome($userId) {
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'income'");
        $stmt->execute([$userId]);
        return (float)$stmt->fetch()['total'];
    }
    
    public function getTotalExpenses($userId) {
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'expense'");
        $stmt->execute([$userId]);
        return (float)$stmt->fetch()['total'];
    }
    
    public function getTotalIncomeByMonth($userId, $monthYear) {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total 
            FROM transactions 
            WHERE user_id = ? AND type = 'income' 
            AND DATE_FORMAT(transaction_date, '%Y-%m') = ?
        ");
        $stmt->execute([$userId, $monthYear]);
        return (float)$stmt->fetch()['total'];
    }
    
    public function getTotalExpensesByMonth($userId, $monthYear) {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total 
            FROM transactions 
            WHERE user_id = ? AND type = 'expense' 
            AND DATE_FORMAT(transaction_date, '%Y-%m') = ?
        ");
        $stmt->execute([$userId, $monthYear]);
        return (float)$stmt->fetch()['total'];
    }
    
    public function getExpensesByCategory($userId, $monthYear) {
        $stmt = $this->pdo->prepare("
            SELECT c.category_name, COALESCE(SUM(t.amount), 0) as total
            FROM transactions t
            JOIN categories c ON t.category_id = c.category_id
            WHERE t.user_id = ? AND t.type = 'expense'
            AND DATE_FORMAT(t.transaction_date, '%Y-%m') = ?
            GROUP BY t.category_id, c.category_name
            ORDER BY total DESC
        ");
        $stmt->execute([$userId, $monthYear]);
        return $stmt->fetchAll();
    }
    
    public function getAvailableMonths($userId) {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT DATE_FORMAT(transaction_date, '%Y-%m') as month_year
            FROM transactions
            WHERE user_id = ?
            ORDER BY month_year DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function getSpentByCategory($userId, $categoryId, $monthYear) {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as spent 
            FROM transactions 
            WHERE user_id = ? AND category_id = ? AND type = 'expense'
            AND DATE_FORMAT(transaction_date, '%Y-%m') = ?
        ");
        $stmt->execute([$userId, $categoryId, $monthYear]);
        return (float)$stmt->fetch()['spent'];
    }
}
