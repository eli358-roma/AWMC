<?php
require_once __DIR__ . '/../models/DictionaryModel.php';

class DictionaryController {
    private $model;
    
    public function __construct() {
        $this->model = new DictionaryModel();
    }

    public function index() {
        // Se è una richiesta AJAX, restituisci JSON
        if ($this->isAjaxRequest()) {
            $search = $_GET['search'] ?? '';
            $page = $_GET['page'] ?? 1;
            $data = $this->model->getAllWords($search, $page);
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
        }

        // Per richieste normali
        $search = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;
        
        $data = $this->model->getAllWords($search, $page);
        
        // Estrai i dati per la view
        $words = $data['data'] ?? [];
        $pagination = [
            'page' => $data['page'] ?? 1,
            'total_pages' => $data['total_pages'] ?? 1,
            'total' => $data['total'] ?? 0
        ];

        // CARICA DIRETTAMENTE IL LAYOUT CON I DATI
        $content = $this->renderView('index.php', [
            'words' => $words,
            'pagination' => $pagination,
            'search' => $search
        ]);
        
        $this->renderLayout($content);
    }
    
    public function create() {
        $message = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'meaning' => $_POST['meaning'] ?? '',
                'chinese' => $_POST['chinese'] ?? '',
                'pronounce' => $_POST['pronounce'] ?? '',
                'note' => $_POST['note'] ?? ''
            ];
            
            if ($this->model->createWord($data)) {
                $_SESSION['message'] = "✅ Parola aggiunta con successo!";
                header("Location: ./");
                exit;
            } else {
                $message = "❌ Errore nell'aggiunta della parola.";
            }
        }
        
        $content = $this->renderView('create.php', [
            'message' => $message
        ]);
        
        $this->renderLayout($content);
    }
    
    public function edit($id) {
        $word = $this->model->getWordById($id);
        $message = '';
        
        if (!$word) {
            $_SESSION['message'] = "❌ Parola non trovata!";
            header("Location: ../");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'meaning' => $_POST['meaning'] ?? '',
                'chinese' => $_POST['chinese'] ?? '',
                'pronounce' => $_POST['pronounce'] ?? '',
                'note' => $_POST['note'] ?? ''
            ];
            
            if ($this->model->updateWord($id, $data)) {
                $_SESSION['message'] = "✅ Parola aggiornata con successo!";
                header("Location: ../");
                exit;
            } else {
                $message = "❌ Errore nell'aggiornamento della parola.";
            }
        }
        
        $content = $this->renderView('edit.php', [
            'word' => $word,
            'message' => $message
        ]);
        
        $this->renderLayout($content);
    }
    
   
    
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
?>