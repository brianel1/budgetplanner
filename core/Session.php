<?php
/**
 * Session Management Class
 * Handles user session operations
 */

class Session {
    private static $started = false;
    
    public static function start() {
        if (!self::$started) {
            $config = require __DIR__ . '/../config/app.php';
            
            session_name($config['session']['name']);
            session_set_cookie_params($config['session']['lifetime']);
            session_start();
            self::$started = true;
        }
    }
    
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    public static function remove($key) {
        self::start();
        unset($_SESSION[$key]);
    }
    
    public static function destroy() {
        self::start();
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        self::$started = false;
    }
    
    public static function isLoggedIn() {
        return self::has('user_id');
    }
    
    public static function getUserId() {
        return self::get('user_id');
    }
    
    public static function getUsername() {
        return self::get('username');
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . self::getBasePath() . '/public/index.php');
            exit();
        }
    }
    
    private static function getBasePath() {
        return dirname($_SERVER['SCRIPT_NAME']);
    }
}
