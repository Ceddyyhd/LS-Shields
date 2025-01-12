// Funktion, um den CSRF-Token aus dem Cookie zu holen
function getCsrfTokenFromCookie() {
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].trim();
        if (cookie.startsWith('csrf_token_public=')) {
            return cookie.substring('csrf_token_public='.length); // Den öffentlichen Token zurückgeben
        }
    }
    return null;  // Rückgabe null, falls der Token nicht gefunden wurde
}

// Interceptor für alle fetch-Anfragen, um den CSRF-Token hinzuzufügen
const originalFetch = window.fetch;

window.fetch = function(url, options = {}) {
    if (options.method === 'POST') {
        const csrfToken = getCsrfTokenFromCookie();  // Hol den Token direkt aus dem Cookie
        if (!csrfToken) {
            console.error('CSRF Token fehlt!');
            return Promise.reject(new Error('CSRF Token fehlt!'));  // Beende die Anfrage, falls kein Token vorhanden ist
        }

        // Token als POST-Parameter hinzuzufügen
        options.body = options.body || new FormData();
        if (options.body instanceof FormData) {
            options.body.append('csrf_token', csrfToken);
        } else {
            options.body['csrf_token'] = csrfToken;
        }
    }
    return originalFetch(url, options);
};
