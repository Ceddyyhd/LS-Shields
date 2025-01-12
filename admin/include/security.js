// Funktion, um den CSRF-Token direkt aus dem HTTP-only Cookie zu holen
function getCsrfTokenFromCookie() {
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].trim();
        if (cookie.startsWith('csrf_token=')) {
            return cookie.substring('csrf_token='.length);
        }
    }
    return null;
}

// Interceptor für alle fetch-Anfragen, um den CSRF-Token hinzuzufügen
const originalFetch = window.fetch;

window.fetch = function(url, options = {}) {
    // Füge den CSRF-Token zu allen POST-Anfragen hinzu
    if (options.method === 'POST') {
        const csrfToken = getCsrfTokenFromCookie();  // Hole den Token aus dem Cookie

        if (!csrfToken) {
            console.error('CSRF Token fehlt!');
            return Promise.reject(new Error('CSRF Token fehlt!'));  // Beende die Anfrage, falls kein Token vorhanden ist
        }

        options.headers = options.headers || {};
        options.headers['Authorization'] = 'Bearer ' + csrfToken;  // CSRF-Token im Header hinzufügen
    }

    // Rufe die Original-`fetch`-Methode auf, wenn keine POST-Anfrage
    return originalFetch(url, options);
};
