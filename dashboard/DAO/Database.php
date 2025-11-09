<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Détecter l'environnement (local vs production)
        $isLocal = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
                   strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
                   strpos($_SERVER['HTTP_HOST'], 'laragon') !== false);
        
        if ($isLocal) {
            // Configuration locale (Laragon)
            $host = 'localhost';
            $dbname = 'club_database';
            $username = 'root';
            $password = '';
        } else {
            // Configuration production (AlwaysData) - utiliser le fichier config.php
            require_once __DIR__ . '/../conf/config.php';
            // Les variables $host, $dbname, $username, $password sont définies dans config.php
        }

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            error_log("Erreur de connexion DB dans Database.php : " . $e->getMessage());
            error_log("Host: $host, DB: $dbname, User: $username");
            die("Erreur de connexion à la base de données.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>

