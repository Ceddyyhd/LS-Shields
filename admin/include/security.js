// Funktion, um den CSRF-Token aus den Cookies zu holen (dies ist der private Token)
// Der private Token wird hier nicht verwendet, aber du kannst ihn speichern, wenn notwendig.
function getCsrfTokenFromCookie() {
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].trim();
        if (cookie.startsWith('csrf_token=')) {
            return cookie.substring('csrf_token='.length);  // Gib den Token zurück
        }
    }
    return null;  // Rückgabe null, wenn der Token nicht gefunden wurde
}

// Funktion, um den öffentlichen Token zu holen (kann vom Server oder lokal gespeichert werden)
function getPublicToken() {
    return document.cookie.replace(/(?:(?:^|.*;\s*)public_token\s*\=\s*([^;]*).*$)|^.*$/, "$1");  // Token aus den Cookies holen
}

// Interceptor für alle fetch-Anfragen, um den CSRF-Token hinzuzufügen
const originalFetch = window.fetch;

window.fetch = function(url, options = {}) {
    // Füge den öffentlichen Token zu allen POST-Anfragen hinzu
    if (options.method === 'POST') {
        const publicToken = getPublicToken();  // Hole den öffentlichen Token

        if (!publicToken) {
            console.error('Öffentlicher Token fehlt!');
            return Promise.reject(new Error('Öffentlicher Token fehlt!'));  // Beende die Anfrage, falls kein öffentlicher Token vorhanden ist
        }

        options.headers = options.headers || {};
        options.headers['Authorization'] = 'Bearer ' + publicToken;  // Füge den öffentlichen Token als Authorization Header hinzu
    }

    // Rufe die Original-`fetch`-Methode auf, wenn keine POST-Anfrage
    return originalFetch(url, options);
};
