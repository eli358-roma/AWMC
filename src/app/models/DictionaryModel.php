<?php
require_once __DIR__ . '/../config/Database.php';

class DictionaryModel {
    private $db;

    public function __construct() {
        try {
            $dbInstance = Database::getInstance();
            $this->db = $dbInstance->getConnection();
            
            if (!$this->db) {
                throw new Exception("Connessione al database fallita");
            }
            
        } catch (Exception $e) {
            die("Errore Model: " . $e->getMessage());
        }
    }

    public function getAllWords($search = '', $page = 1, $perPage = 30) {
        try {
            $offset = ($page - 1) * $perPage;
            $where = '';
            $params = [];

            //condizione per ricercare la parola che si è cercata
            if (!empty($search)) {
                $where = "WHERE meaning LIKE :search OR chinese LIKE :search OR pronounce LIKE :search";
                $params[':search'] = "%$search%";
            }

            // Query principale per visualizzare tutte le parole del database
            $query = "SELECT * FROM dictionary $where 
                     ORDER BY meaning  
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $words = $stmt->fetchAll();

            // Query conteggio totale
            $countQuery = "SELECT COUNT(*) as total FROM dictionary $where";
            $countStmt = $this->db->prepare($countQuery);
            
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            
            $countStmt->execute();
            $total = $countStmt->fetch()['total'];
            
            return [
                'success' => true,
                'data' => $words,
                'total' => $total,
                'page' => (int)$page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage)
            ];

        } catch (PDOException $e) {
            error_log("Errore in getAllWords: " . $e->getMessage());
            return [
                'success' => false,
                'data' => [],
                'total' => 0,
                'page' => 1,
                'per_page' => $perPage,
                'total_pages' => 0
            ];
        }
    }

    //prendere la parola dall'id
    public function getWordById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM dictionary WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Errore in getWordById: " . $e->getMessage());
            return false;
        }
    }

    //funzione con query per la creazione della parola
    public function createWord($data) {
        try {
            $stmt = $this->db->prepare("INSERT INTO dictionary 
                (meaning, chinese, pronounce, note) 
                VALUES (:meaning, :chinese, :pronounce, :note)");
                
            $stmt->bindValue(':meaning', $data['meaning']);
            $stmt->bindValue(':chinese', $data['chinese']);
            $stmt->bindValue(':pronounce', $data['pronounce']);
            $stmt->bindValue(':note', $data['note'] ?? '');
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Errore in createWord: " . $e->getMessage());
            return false;
        }
    }

    //funzione con query per la modifica della parola
    public function updateWord($id, $data) {
        try {
            $stmt = $this->db->prepare("UPDATE dictionary SET 
                meaning = :meaning, 
                chinese = :chinese, 
                pronounce = :pronounce, 
                note = :note 
                WHERE id = :id");
                
            $stmt->bindValue(':meaning', $data['meaning']);
            $stmt->bindValue(':chinese', $data['chinese']);
            $stmt->bindValue(':pronounce', $data['pronounce']);
            $stmt->bindValue(':note', $data['note'] ?? '');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Errore in updateWord: " . $e->getMessage());
            return false;
        }
    }

    //funzione con query per controllare se ci sono parole duplicate
    public function getDuplicateWords($chinese, $pronounce, $excludeId = null) {
        try {
            $query = "SELECT * FROM dictionary 
                    WHERE (chinese = :chinese AND pronounce = :pronounce)";
            
            if ($excludeId) {
                $query .= " AND id != :exclude_id";
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':chinese', $chinese);
            $stmt->bindValue(':pronounce', $pronounce);
            
            if ($excludeId) {
                $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Errore in getDuplicateWords: " . $e->getMessage());
            return [];
        }
    }
}
?>
