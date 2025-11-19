// Funzione per tastiera virtuale
function insertChar(char) {
    const pronounceField = document.getElementById('pronounce');
    if (pronounceField) {
        pronounceField.value += char;
        pronounceField.focus();
    }
}

// Conferma eliminazione
function confirmDelete(word) {
    return confirm(`Sei sicuro di voler eliminare "${word}"?`);
}

// Auto-hide messages
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide messages after 5 seconds
    const messages = document.querySelectorAll('.message');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            message.style.transition = 'opacity 0.5s';
            setTimeout(() => message.remove(), 500);
        }, 5000);
    });
    
    // Focus sul campo di ricerca
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.focus();
    }
});

console.log("✅ Chinese Dictionary loaded successfully!");