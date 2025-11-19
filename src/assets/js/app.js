//Funzione per la tastiera virtuale
function insertChar(char) {
    const pronounceField = document.getElementById('pronounce');
    if (pronounceField) {
        pronounceField.value += char;
        pronounceField.focus();
    }
}

//messaggio per conferma l'eliminazione
function confirmDelete(word) {
    return confirm(`Sei sicuro di voler eliminare "${word}"?`);
}

//comando per nascondere automaticamente i messaggi che verranno visualizzati sulla pagina
document.addEventListener('DOMContentLoaded', function() {
    // Messaggi Auto-hide dopo 5 secondi
    const messages = document.querySelectorAll('.message');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            message.style.transition = 'opacity 0.5s';
            setTimeout(() => message.remove(), 500);
        }, 5000);
    });
    
    //Focus sul campo di ricerca
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.focus();
    }
});

console.log("✅ Chinese Dictionary loaded successfully!");