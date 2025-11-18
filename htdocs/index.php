<?php

// VERSIONE SEMPLIFICATA - SESSIONI CHE SCADONO DOPO 5 minuti
session_start();

// ROUTER SEMPLICE che decide quale "view diretta" caricare
$request = $_SERVER['REQUEST_URI'] ?? '/';
//$method = $_SERVER['REQUEST_METHOD'];

// CONFIGURAZIONE LOGIN SEMPLIFICATA
$config_password = '&Niki&358!!';

// TIMEOUT SESSIONE (5 minuti)
$session_timeout = 300;

// Funzione per verificare il login con timeout
function isLoggedIn() {
    global $session_timeout;
    
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        // Verifica timeout
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $session_timeout)) {
            // Sessione scaduta
            session_destroy();
            return false;
        }
        return true;
    }
    return false;
}

// Funzione per richiedere il login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /login");
        exit;
    }
}

// Gestione login + routing semplice da modificare poi
if (strpos($request, '/login') !== false) {
    $error = '';
    
    // Se già loggato, reindirizza alla home
    if (isLoggedIn()) {
        header("Location: /");
        exit;
    }
    
    // Gestione del form di login
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        
        if (!empty($password)) {
            global $config_password;
            
            if ($password === $config_password) {
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = 'Amministratore';
                $_SESSION['login_time'] = time(); // Timestamp del login
                header("Location: /");
                exit;
            } else {
                $error = "Password non valida!";
            }
        } else {
            $error = "Inserisci la password!";
        }
    }
    
    // Mostra la pagina di login
    ?>
    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Chinese Dictionary</title>
        <link rel="stylesheet" href="/assets/css/style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="login-container">
            <div class="login-header">
                <h1>Chinese Dictionary</h1>
                <p class="text-muted">Inserisci la password per accedere alle modifiche</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Accedi</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Gestione logout
if (strpos($request, '/logout') !== false) {
    session_destroy();
    header("Location: /login");
    exit;
}else{
    // Per tutte le altre pagine, richiedi il login
	requireLogin();

	// Carica sempre il database e model (solo se loggato)
	require_once __DIR__ . '/app/config/Database.php';
	require_once __DIR__ . '/app/models/DictionaryModel.php';

	$model = new DictionaryModel();

	if (strpos($request, '/create') !== false) {
    require __DIR__ . '/views/create-direct.php';
	} elseif (preg_match('/\/edit\/(\d+)/', $request, $matches)) {
    	$word = $model->getWordById($matches[1]);
    	require __DIR__ . '/views/edit-direct.php';
	} else {
    	$search = $_GET['search'] ?? '';
    	$page = $_GET['page'] ?? 1;
    	$data = $model->getAllWords($search, $page);
    	$words = $data['data'] ?? [];
    	$pagination = [
        	'page' => $data['page'] ?? 1,
        	'total_pages' => $data['total_pages'] ?? 1,
        	'total' => $data['total'] ?? 0
    	];
    	require __DIR__ . '/views/index-direct.php';
	}
}
?>