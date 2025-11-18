<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chinese Dictionary</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

</head>
<body>
    <div class="container">
        <div class="header-flex">    
            <h1>Chinese Dictionary</h1>
            <small class="text-muted">Benvenuto, <?= htmlspecialchars($_SESSION['username'] ?? 'Utente') ?></small>
            <div>
                <a href="/create" class="btn btn-danger">Aggiungi Nuova Parola</a>
                <a href="/logout" class="btn btn-outline-secondary">Logout</a>
            </div>
        </div>
            <form method="get" class="search-form">
            <input type="text" name="search" class="label-search bi bi-search" placeholder=" Cerca parola..." 
                   value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit" class="btn btn-secondary btn-sm">Cerca</button>
            <?php if (!empty($search)): ?>
                <a href="/" class="btn btn-dark btn-sm">Reset</a>
            <?php endif; ?>
        </form><br>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="table-responsive">
            <?php if (empty($words)): ?>
                <div class="message">
                    <?php if (!empty($search)): ?>
                        🔍 Nessuna parola trovata per "<?= htmlspecialchars($search) ?>"
                    <?php else: ?>
                        📝 Nessuna parola nel database. <a href="/create">Aggiungi la prima!</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Significato</th>
                            <th scope="col">Cinese</th>
                            <th scope="col">Pronuncia</th>
                            <th scope="col">Caratteristiche</th>
                            <th scope="col"> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($words as $word): ?>
                            <tr>
                                <td data-label="Significato"><?= htmlspecialchars($word['meaning']) ?></td>
                                <td data-label="Cinese" class="chinese-font"><?= htmlspecialchars($word['chinese']) ?></td>
                                <td data-label="Pronuncia"><?= htmlspecialchars($word['pronounce']) ?></td>
                                <td data-label="Note"><?= htmlspecialchars($word['note'] ?? '') ?></td>
                                <td data-label="Azioni">
                                    <a href="/edit/<?= $word['id'] ?>" class="btn btn-outline-primary">Modifica</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
        <div class="pagination">
            <?php if (($pagination['page'] ?? 1) > 1): ?>
                <a href="?page=<?= $pagination['page'] - 1 ?>&search=<?= urlencode($search ?? '') ?>" 
                   class="button bi bi-arrow-bar-left"> <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-arrow-left-square-fill" viewBox="0 0 16 16">
  <path d="M16 14a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2zm-4.5-6.5H5.707l2.147-2.146a.5.5 0 1 0-.708-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 0 0 .708-.708L5.707 8.5H11.5a.5.5 0 0 0 0-1"/>
</svg> </a>
            <?php endif; ?>
            
            <span style="margin-left:10px; margin-right:10px;"> Pagina <?= $pagination['page'] ?? 1 ?> di <?= $pagination['total_pages'] ?? 1 ?></span>
            
            <?php if (($pagination['page'] ?? 1) < ($pagination['total_pages'] ?? 1)): ?>
                <a href="?page=<?= $pagination['page'] + 1 ?>&search=<?= urlencode($search ?? '') ?>" 
                   class="button"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-arrow-right-square-fill" viewBox="0 0 16 16">
  <path d="M0 14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2zm4.5-6.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5a.5.5 0 0 1 0-1"/>
</svg></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="/assets/js/app.js"></script>

</body>

</html>