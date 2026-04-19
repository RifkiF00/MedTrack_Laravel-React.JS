<?php
/**
 * Database.php
 * PDO MySQL Connection Class
 */

class Database {
    private $host = 'localhost';
    private $db = 'medtrack_ipsrs_db';
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';
    
    public function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";

            $pdo = new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            return $pdo;
        } catch (PDOException $e) {
            die('❌ Database Connection Error: ' . $e->getMessage());
        }
    }
}
?>
