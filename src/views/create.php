<!--Interfaccia della pagina per la creazione di una nuova parola-->
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Parola - Chinese Dictionary</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header-flex">
            <h1>Aggiungi Nuova Parola</h1>
            <a href="/admin" class="btn btn-secondary btn-back btn-lg">Annulla</a>
        </div>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="meaning">Significato*:</label>
                <input type="text" id="meaning" name="meaning" required 
                       value="<?= htmlspecialchars($_POST['meaning'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="chinese">Caratteri Cinesi*:</label>
                <input type="text" id="chinese" name="chinese" required 
                       value="<?= htmlspecialchars($_POST['chinese'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="pronounce">Pronuncia (Pinyin)*:</label>
                <input type="text" id="pronounce" name="pronounce" required 
                       value="<?= htmlspecialchars($_POST['pronounce'] ?? '') ?>">
                
                <div class="keyboard">
                    <p><strong>Tastiera toni:</strong></p>
                    <?php foreach ($vowels as $v): ?>
                        <button type="button" onclick="insertChar('<?= $v ?>')"><?= $v ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="note">Caratteristiche:</label>
                <textarea id="note" name="note" rows="4"><?= htmlspecialchars($_POST['note'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salva</button>
            </div>
        </form>
    </div>

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