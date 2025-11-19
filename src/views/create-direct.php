<?php
$message = '';

// Gestione del form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../app/config/Database.php';
    require_once __DIR__ . '/../app/models/DictionaryModel.php';
    
    $model = new DictionaryModel();
    $data = [
        'meaning' => $_POST['meaning'] ?? '',
        'chinese' => $_POST['chinese'] ?? '',
        'pronounce' => $_POST['pronounce'] ?? '',
        'note' => $_POST['note'] ?? ''
    ];
    
    // CONTROLLO SE LA PAROLA ESISTE GIÀ
    if ($model->wordExists($data['meaning'], $data['chinese'], $data['pronounce'])) {
        $duplicates = $model->getDuplicateWords($data['meaning'], $data['chinese'], $data['pronounce']);
        $message = "❌ Questa parola esiste già nel database!";
    } else {
        // Se non esiste, procedi con la creazione
        if ($model->createWord($data)) {
            $_SESSION['message'] = "✅ Parola aggiunta con successo!";
            header("Location: /admin");
            exit;
        } else {
            $message = "❌ Errore nell'aggiunta della parola.";
        }
    }
}

// Definisci le vocali con toni FUORI dall'HTML
$vowels = array(
    'ā', 'á', 'ǎ', 'à',
    'ē', 'é', 'ě', 'è', 
    'ī', 'í', 'ǐ', 'ì',
    'ō', 'ó', 'ǒ', 'ò',
    'ū', 'ú', 'ǔ', 'ù',
    'ǖ', 'ǘ', 'ǚ', 'ǜ'
);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Parola - Chinese Dictionary</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

</head>
<body>
    <div class="container">
        <div class="header-flex">
            <h1>Aggiungi Nuova Parola </h1>
            <a href="/admin" class="btn btn-secondary btn-back btn-lg">Annulla</a>  
        </div>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="message error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="meaning">Significato*:</label>
                <input type="text" id="meaning" name="meaning" required 
                       value="<?= htmlspecialchars($_POST['meaning'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="chinese">Parola Cinese*:</label>
                <input type="text" id="chinese" name="chinese" required 
                       value="<?= htmlspecialchars($_POST['chinese'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="pronounce">Pronuncia (Pinyin)*:</label>
                <input type="text" id="pronounce" name="pronounce" required 
                       value="<?= htmlspecialchars($_POST['pronounce'] ?? '') ?>">
                
                <div class="keyboard">
                    <p><strong>Tastiera toni:</strong></p>
                    <?php
                    // Usa l'array definito sopra
                    foreach ($vowels as $v) {
                        echo "<button type='button' onclick=\"insertChar('$v')\">$v</button>";
                    }
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="note">Note:</label>
                <textarea id="note" name="note" rows="4"><?= htmlspecialchars($_POST['note'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary ">Salva</button>
            </div>
        </form>
    </div>

    <script src="/assets/js/app.js"></script>
    
    <script>
    function insertChar(char) {
        const pronounceField = document.getElementById('pronounce');
        if (pronounceField) {
            pronounceField.value += char;
            pronounceField.focus();
        }
    }
    </script>
</body>
</html>