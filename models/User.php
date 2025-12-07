<?php
/**
 * User Model
 * Handles user-related database operations
 */

class User {
    private $pdo;
    
    public function __construct($pdo = null) {
        $this->pdo = $pdo ?? App::db();
    }
    
    public function create($username, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);
        return $this->pdo->lastInsertId();
    }
    
    public function findById($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function emailExists($email, $excludeUserId = null) {
        if ($excludeUserId) {
            $stmt = $this->pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
            $stmt->execute([$email, $excludeUserId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
        }
        return $stmt->rowCount() > 0;
    }
    
    public function verifyPassword($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    public function update($userId, $username, $email) {
        $stmt = $this->pdo->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
        return $stmt->execute([$username, $email, $userId]);
    }
    
    public function delete($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
