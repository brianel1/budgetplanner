<?php
/**
 * Category Model
 * Handles category-related database operations
 */

class Category {
    private $pdo;
    
    public function __construct($pdo = null) {
        $this->pdo = $pdo ?? App::db();
    }
    
    public function create($userId, $categoryName, $type) {
        $stmt = $this->pdo->prepare("INSERT INTO categories (user_id, category_name, type) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $categoryName, $type]);
        return $this->pdo->lastInsertId();
    }
    
    public function findById($categoryId) {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetch();
    }
    
    public function findByIdAndUser($categoryId, $userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE category_id = ? AND user_id = ?");
        $stmt->execute([$categoryId, $userId]);
        return $stmt->fetch();
    }
    
    public function getAllByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY type ASC, category_name ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getByType($userId, $type) {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE user_id = ? AND type = ? ORDER BY category_name ASC");
        $stmt->execute([$userId, $type]);
        return $stmt->fetchAll();
    }
    
    public function update($categoryId, $userId, $categoryName, $type) {
        $stmt = $this->pdo->prepare("UPDATE categories SET category_name = ?, type = ? WHERE category_id = ? AND user_id = ?");
        return $stmt->execute([$categoryName, $type, $categoryId, $userId]);
    }
    
    public function delete($categoryId, $userId) {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE category_id = ? AND user_id = ?");
        return $stmt->execute([$categoryId, $userId]);
    }
    
    public function belongsToUser($categoryId, $userId) {
        $stmt = $this->pdo->prepare("SELECT category_id FROM categories WHERE category_id = ? AND user_id = ?");
        $stmt->execute([$categoryId, $userId]);
        return $stmt->rowCount() > 0;
    }
}
