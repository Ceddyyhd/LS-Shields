// Funktion, um den öffentlichen CSRF-Token aus dem Cookie zu holen
function getCsrfTokenFromCookie() {
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].trim();
        if (cookie.startsWith('csrf_token_public=')) {
            return cookie.substring('csrf_token_public='.length);  // Gib den öffentlichen Token zurück
        }
    }
    return null;  // Rückgabe null, falls der Token nicht gefunden wurde
}

// Interceptor für alle fetch-Anfragen, um den CSRF-Token hinzuzufügen
const originalFetch = window.fetch;

window.fetch = function(url, options = {}) {
    if (options.method === 'POST') {
        // Hole den öffentlichen CSRF-Token aus dem Cookie
        const csrfToken = getCsrfTokenFromCookie(); 

        if (!csrfToken) {
            console.error('CSRF Token fehlt!');
            return Promise.reject(new Error('CSRF Token fehlt!'));  // Beende die Anfrage, falls kein Token vorhanden ist
        }

        options.headers = options.headers || {};
        options.headers['Authorization'] = 'Bearer ' + csrfToken;  // Füge den öffentlichen CSRF-Token in den Header ein

        return originalFetch(url, options);  // Sende die Anfrage mit dem Token im Header
    } else {
        return originalFetch(url, options);  // Rufe die Original-`fetch`-Methode auf, wenn keine POST-Anfrage
    }
};
