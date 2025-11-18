<?php
class Database {
    private static $instance;
    private $db;

    private function __construct() {
        try {
            // CORREGGI: Password vuota per UniServerZ
            $host = 'sql313.infinityfree.com';
            $dbname = 'if0_40406834_chinese_dictionary';
            $username = 'if0_40406834';
            $password = 'Niki00358';

            $this->db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Errore database: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->db;
    }

    private function initializeDatabase() {
        $sql = "CREATE TABLE IF NOT EXISTS dictionary (
            id INT PRIMARY KEY AUTO_INCREMENT,
            meaning TEXT NOT NULL,
            chinese VARCHAR(255) NOT NULL,
            pronounce VARCHAR(255) NOT NULL,
            note TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        $this->db->exec($sql);
    }

    public function debugConnection() {
        echo "<div style='background:#f0f0f0; padding:15px; margin-bottom:20px; border-radius:5px;'>";
        echo "<h3 style='color:#d12727;'>Debug Connessione Database</h3>";

        echo "<p><strong>Driver:</strong> MySQL</p>";
        echo "<p><strong>Estensione PDO:</strong> " . (extension_loaded('pdo_mysql') ? '✅ Caricata' : '❌ Non caricata') . "</p>";

        if ($this->db) {
            echo "<p><strong>Stato connessione:</strong> ✅ Attiva</p>";
            echo "<p><strong>Database:</strong> chinese_dictionary</p>";
        } else {
            echo "<p><strong>Stato connessione:</strong> ❌ Fallita</p>";
        }
        echo "</div>";
    }
}
?>