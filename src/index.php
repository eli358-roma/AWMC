<?php
// FRONT CONTROLLER - MVC COMPLETO
session_start();

// Configurazioni
require_once __DIR__ . '/app/config/Database.php';
require_once __DIR__ . '/app/controllers/DictionaryController.php';

$config_password = '&Niki&358!!';
$session_timeout = 300;

// Funzione helper per login
function isLoggedIn() {
    global $session_timeout;
    
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $session_timeout)) {
            session_destroy();
            return false;
        }
        return true;
    }
    return false;
}

// Istanza del controller
$controller = new DictionaryController();

// Routing MVC
$request = $_SERVER['REQUEST_URI'] ?? '/';

switch (true) {
    case $request === '/' || strpos($request, '/?') === 0:
        $controller->publicIndex();
        break;
        
    case strpos($request, '/login') !== false:
        handleLogin($controller);
        break;
        
    case strpos($request, '/logout') !== false:
        session_destroy();
        header("Location: /");
        exit;
        
    case strpos($request, '/admin') !== false:
        if (!isLoggedIn()) {
            header("Location: /login");
            exit;
        }
        handleAdminRoutes($request, $controller);
        break;
        
    default:
        header("HTTP/1.0 404 Not Found");
        echo "Pagina non trovata";
        exit;
}

// Gestione login separata
function handleLogin($controller) {
    global $config_password;
    
    if (isLoggedIn()) {
        header("Location: /admin");
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        
        if (!empty($password) && $password === $config_password) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = 'Amministratore';
            $_SESSION['login_time'] = time();
            header("Location: /admin");
            exit;
        } else {
            $error = "Password non valida!";
            showLoginPage($error);
        }
    } else {
        showLoginPage();
    }
}

// Gestione routes admin
function handleAdminRoutes($request, $controller) {
    if (strpos($request, '/admin/create') !== false) {
        $controller->create();
    } elseif (preg_match('/\/admin\/edit\/(\d+)/', $request, $matches)) {
        $controller->edit($matches[1]);
    } else {
        $controller->adminIndex();
    }
}

// Pagina login
function showLoginPage($error = '') {
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