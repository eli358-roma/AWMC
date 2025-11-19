<?php

// VERSIONE SEMPLIFICATA - SESSIONI CHE SCADONO DOPO 5 minuti
session_start();

// ROUTER SEMPLICE che decide quale "view diretta" caricare
$request = $_SERVER['REQUEST_URI'] ?? '/';
//$method = $_SERVER['REQUEST_METHOD'];

// CONFIGURAZIONE LOGIN
$config_password = '&Niki&358!!';
$session_timeout = 300; //5 minuti

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

//caricamento del database
require_once __DIR__ . '/app/config/Database.php';
require_once __DIR__ . '/app/models/DictionaryModel.php';
$model = new DictionaryModel();

$request =$_SERVER['REQUEST_URI']??'/';

// ROUTING
switch (true) {
    case strpos($request, '/login') !== false:
        handleLogin();
        break;
        
    case strpos($request, '/logout') !== false:
        session_destroy();
        header("Location: /");
        exit;
        
    case strpos($request, '/admin') !== false:
        requireLogin();
        handleAdminRoutes($request, $model);
        break;
        
    default:
        // AREA PUBBLICA - Visualizzazione dizionario
        handlePublicArea($model);
        break;
}

function handleLogin() {
    global $config_password;
    
    // Se già loggato, reindirizza all'admin
    if (isLoggedIn()) {
        header("Location: /admin");
        exit;
    }
    
    $error = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        
        if (!empty($password)) {
            if ($password === $config_password) {
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = 'Amministratore';
                $_SESSION['login_time'] = time();
                header("Location: /admin");
                exit;
            } else {
                $error = "Password non valida!";
            }
        } else {
            $error = "Inserisci la password!";
        }
    }
    
    // Mostra pagina di login
    showLoginPage($error);
}

// FUNZIONE PER AREA PUBBLICA
function handlePublicArea($model) {
    $search = $_GET['search'] ?? '';
    $page = $_GET['page'] ?? 1;
    
    $data = $model->getAllWords($search, $page);
    $words = $data['data'] ?? [];
    $pagination = [
        'page' => $data['page'] ?? 1,
        'total_pages' => $data['total_pages'] ?? 1,
        'total' => $data['total'] ?? 0
    ];
    
    // Carica la view pubblica
    require __DIR__ . '/views/public-index.php';
}

// FUNZIONE PER ROTTE ADMIN
function handleAdminRoutes($request, $model) {
    if (strpos($request, '/admin/create') !== false) {
        require __DIR__ . '/views/create-direct.php';
    } elseif (preg_match('/\/admin\/edit\/(\d+)/', $request, $matches)) {
        $word = $model->getWordById($matches[1]);
        require __DIR__ . '/views/edit-direct.php';
    } else {
        // Dashboard admin - lista parole con pulsanti modifica
        $search = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;
        
        $data = $model->getAllWords($search, $page);
        $words = $data['data'] ?? [];
        $pagination = [
            'page' => $data['page'] ?? 1,
            'total_pages' => $data['total_pages'] ?? 1,
            'total' => $data['total'] ?? 0
        ];
        
        require __DIR__ . '/views/admin-index.php';
    }
}

// FUNZIONE PER PAGINA DI LOGIN
function showLoginPage($error) {
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
                <p class="text-muted">Area Amministrativa</p>
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
                <div class="text-center mt-3">
                    <a href="/" class="btn btn-outline-secondary btn-sm">Torna al Dizionario</a>
                </div>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>
