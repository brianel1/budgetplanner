<?php
/**
 * Budget Model
 * Handles budget-related database operations
 */

class Budget {
    private $pdo;
    
    public function __construct($pdo = null) {
        $this->pdo = $pdo ?? App::db();
    }
    
    public function create($userId, $categoryId, $monthlyAmount, $monthYear) {
        $stmt = $this->pdo->prepare("INSERT INTO budgets (user_id, category_id, monthly_amount, month_year) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $categoryId, $monthlyAmount, $monthYear]);
        return $this->pdo->lastInsertId();
    }
    
    public function findById($budgetId) {
        $stmt = $this->pdo->prepare("SELECT * FROM budgets WHERE budget_id = ?");
        $stmt->execute([$budgetId]);
        return $stmt->fetch();
    }
    
    public function findByIdAndUser($budgetId, $userId) {
        $stmt = $this->pdo->prepare("
            SELECT b.*, c.category_name 
            FROM budgets b
            JOIN categories c ON b.category_id = c.category_id
            WHERE b.budget_id = ? AND b.user_id = ?
        ");
        $stmt->execute([$budgetId, $userId]);
        return $stmt->fetch();
    }
    
    public function getAllByUser($userId) {
        $stmt = $this->pdo->prepare("
            SELECT b.*, c.category_name,
                   COALESCE((
                       SELECT SUM(t.amount) 
                       FROM transactions t 
                       WHERE t.category_id = b.category_id 
                         AND t.user_id = b.user_id
                         AND t.type = 'expense'
                         AND DATE_FORMAT(t.transaction_date, '%Y-%m') = b.month_year
                   ), 0) as spent
            FROM budgets b
            JOIN categories c ON b.category_id = c.category_id
            WHERE b.user_id = ?
            ORDER BY b.month_year DESC, c.category_name ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function exists($userId, $categoryId, $monthYear, $excludeBudgetId = null) {
        if ($excludeBudgetId) {
            $stmt = $this->pdo->prepare("SELECT budget_id FROM budgets WHERE user_id = ? AND category_id = ? AND month_year = ? AND budget_id != ?");
            $stmt->execute([$userId, $categoryId, $monthYear, $excludeBudgetId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT budget_id FROM budgets WHERE user_id = ? AND category_id = ? AND month_year = ?");
            $stmt->execute([$userId, $categoryId, $monthYear]);
        }
        return $stmt->rowCount() > 0;
    }
    
    public function update($budgetId, $userId, $categoryId, $monthlyAmount, $monthYear) {
        $stmt = $this->pdo->prepare("UPDATE budgets SET category_id = ?, monthly_amount = ?, month_year = ? WHERE budget_id = ? AND user_id = ?");
        return $stmt->execute([$categoryId, $monthlyAmount, $monthYear, $budgetId, $userId]);
    }
    
    public function delete($budgetId, $userId) {
        $stmt = $this->pdo->prepare("DELETE FROM budgets WHERE budget_id = ? AND user_id = ?");
        return $stmt->execute([$budgetId, $userId]);
    }
    
    public function belongsToUser($budgetId, $userId) {
        $stmt = $this->pdo->prepare("SELECT budget_id FROM budgets WHERE budget_id = ? AND user_id = ?");
        $stmt->execute([$budgetId, $userId]);
        return $stmt->rowCount() > 0;
    }
}
