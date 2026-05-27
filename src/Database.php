<?php
 
class Database
{
    private static ?PDO $instance = null;
 
    private function __construct() {}
 
    private function __clone() {}
 
    public static function connect(): PDO
    {
        if (self::$instance === null) {
            self::loadEnv();
 
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $name = $_ENV['DB_NAME'] ?? '';
            $user = $_ENV['DB_USER'] ?? '';
            $pass = $_ENV['DB_PASS'] ?? '';
 
            $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
 
            self::$instance = new PDO($dsn, $user, $pass, [
                // Kasta ett undantag vid SQL-fel istället för tyst misslyckande
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
 
                // fetch() returnerar associativa arrayer som standard
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
 
                // Stäng av emulerade prepared statements — säkrare
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
 
        return self::$instance;
    }
 
    private static function loadEnv(): void
    {
        $path = dirname(__DIR__) . '/.env';
 
        if (!file_exists($path)) {
            return; 
            }
 
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
 
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }
 
            [$key, $value] = explode('=', $line, 2);
 
            $key = trim($key);
            $value = trim($value);
 
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}
