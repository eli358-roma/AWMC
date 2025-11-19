<?php
require_once __DIR__ . '/../models/DictionaryModel.php';

class DictionaryController {
    private $model;
    
    public function __construct() {
        $this->model = new DictionaryModel();
    }

    /**
     * Action per l'area pubblica
     */
    public function publicIndex() {
        $search = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;
        
        $data = $this->model->getAllWords($search, $page);
        $words = $data['data'] ?? [];
        $pagination = [
            'page' => $data['page'] ?? 1,
            'total_pages' => $data['total_pages'] ?? 1,
            'total' => $data['total'] ?? 0
        ];
        
        $this->render('public-index', [
            'words' => $words,
            'pagination' => $pagination,
            'search' => $search
        ]);
    }

    /**
     * Action per l'area amministrativa
     */
    public function adminIndex() {
        $search = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;
        
        $data = $this->model->getAllWords($search, $page);
        $words = $data['data'] ?? [];
        $pagination = [
            'page' => $data['page'] ?? 1,
            'total_pages' => $data['total_pages'] ?? 1,
            'total' => $data['total'] ?? 0
        ];
        
        $this->render('admin-index', [
            'words' => $words,
            'pagination' => $pagination,
            'search' => $search
        ]);
    }

    /**
     * Action per creare una nuova parola
     */
    public function create() {
        $message = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'meaning' => $_POST['meaning'] ?? '',
                'chinese' => $_POST['chinese'] ?? '',
                'pronounce' => $_POST['pronounce'] ?? '',
                'note' => $_POST['note'] ?? ''
            ];
            
            // Controllo duplicati
            if ($this->model->wordExists($data['meaning'], $data['chinese'], $data['pronounce'])) {
                $message = "❌ Questa parola esiste già nel database!";
            } else {
                if ($this->model->createWord($data)) {
                    $_SESSION['message'] = "✅ Parola aggiunta con successo!";
                    header("Location: /admin");
                    exit;
                } else {
                    $message = "❌ Errore nell'aggiunta della parola.";
                }
            }
        }
        
        $vowels = [
            'ā', 'á', 'ǎ', 'à',
            'ē', 'é', 'ě', 'è', 
            'ī', 'í', 'ǐ', 'ì',
            'ō', 'ó', 'ǒ', 'ò',
            'ū', 'ú', 'ǔ', 'ù',
            'ǖ', 'ǘ', 'ǚ', 'ǜ'
        ];
        
        $this->render('create', [
            'message' => $message,
            'vowels' => $vowels
        ]);
    }

    /**
     * Action per modificare una parola esistente
     */
    public function edit($id) {
        $word = $this->model->getWordById($id);
        $message = '';
        
        if (!$word) {
            $_SESSION['message'] = "❌ Parola non trovata!";
            header("Location: /admin");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'meaning' => $_POST['meaning'] ?? '',
                'chinese' => $_POST['chinese'] ?? '',
                'pronounce' => $_POST['pronounce'] ?? '',
                'note' => $_POST['note'] ?? ''
            ];
            
            // Controllo duplicati (escludendo la parola corrente)
            if ($this->model->wordExists($data['meaning'], $data['chinese'], $data['pronounce'], $id)) {
                $message = "❌ Questa parola esiste già nel database!";
            } else {
                if ($this->model->updateWord($id, $data)) {
                    $_SESSION['message'] = "✅ Parola aggiornata con successo!";
                    header("Location: /admin");
                    exit;
                } else {
                    $message = "❌ Errore nell'aggiornamento della parola.";
                }
            }
        }
        
        $vowels = [
            'ā', 'á', 'ǎ', 'à',
            'ē', 'é', 'ě', 'è', 
            'ī', 'í', 'ǐ', 'ì',
            'ō', 'ó', 'ǒ', 'ò',
            'ū', 'ú', 'ǔ', 'ù',
            'ǖ', 'ǘ', 'ǚ', 'ǜ'
        ];
        
        $this->render('edit', [
            'word' => $word,
            'message' => $message,
            'vowels' => $vowels
        ]);
    }

    /**
     * Metodo helper per il rendering delle views
     */
    private function render($view, $data = []) {
        extract($data);
        require __DIR__ . "/../../views/{$view}.php";
    }
}
?>