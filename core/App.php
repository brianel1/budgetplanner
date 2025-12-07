<?php
/**
 * Application Bootstrap Class
 * Initializes the application and loads required components
 */

class App {
    private static $initialized = false;
    
    public static function init() {
        if (self::$initialized) {
            return;
        }
        
        // Load configuration
        $config = require __DIR__ . '/../config/app.php';
        
        // Set timezone
        date_default_timezone_set($config['timezone']);
        
        // Set error reporting based on debug mode
        if ($config['debug']) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
        
        // Autoload classes
        spl_autoload_register([self::class, 'autoload']);
        
        self::$initialized = true;
    }
    
    private static function autoload($class) {
        $paths = [
            __DIR__ . '/../core/',
            __DIR__ . '/../models/',
            __DIR__ . '/../controllers/',
        ];
        
        foreach ($paths as $path) {
            $file = $path . $class . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
    
    public static function db() {
        return Database::getInstance()->getConnection();
    }
    
    public static function redirect($url) {
        header("Location: $url");
        exit();
    }
    
    public static function view($name, $data = []) {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $name . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            throw new Exception("View not found: $name");
        }
    }
}
