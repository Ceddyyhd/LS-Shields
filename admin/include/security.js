// Funktion, um den CSRF-Token vom Server zu holen
function getCsrfToken() {
    return fetch('get_csrf_token.php', {
        method: 'GET',  // Hole den Token über eine GET-Anfrage
        credentials: 'same-origin',  // Cookie wird mitgesendet
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            return data.csrf_token; // Gib den Token zurück
        } else {
            throw new Error('CSRF Token konnte nicht abgerufen werden');
        }
    });
}

// Interceptor für alle fetch-Anfragen, um den CSRF-Token hinzuzufügen
const originalFetch = window.fetch;

window.fetch = function(url, options = {}) {
    // Füge den CSRF-Token zu allen POST-Anfragen hinzu
    if (options.method === 'POST') {
        getCsrfToken().then(csrfToken => {
            options.headers = options.headers || {};
            options.headers['Authorization'] = 'Bearer ' + csrfToken;  // CSRF-Token im Header hinzufügen
            originalFetch(url, options);  // Sende die Anfrage mit dem Token im Header
        }).catch(error => {
            console.error('Fehler beim Abrufen des CSRF-Tokens:', error);
        });
    } else {
        // Rufe die Original-`fetch`-Methode auf, wenn keine POST-Anfrage
        return originalFetch(url, options);
    }
};
