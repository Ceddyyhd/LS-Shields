// Funktion, um den CSRF-Token direkt aus dem Cookie zu holen
function getCsrfTokenFromCookie() {
    // Hole den CSRF-Token aus den Cookies
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].trim();
        if (cookie.startsWith('csrf_token=')) {
            return cookie.substring('csrf_token='.length, cookie.length);  // Gib den Token zurück
        }
    }
    return null;  // Rückgabe null, falls der Token nicht gefunden wurde
}

// Interceptor für alle fetch-Anfragen, um den CSRF-Token hinzuzufügen
const originalFetch = window.fetch;

window.fetch = function(url, options = {}) {
    // Füge den CSRF-Token zu allen POST-Anfragen hinzu
    if (options.method === 'POST') {
        const csrfToken = getCsrfTokenFromCookie();  // Hol den Token direkt aus dem Cookie

        if (!csrfToken) {
            console.error('CSRF Token fehlt!');
            return; // Beende die Anfrage, falls kein Token vorhanden ist
        }

        options.headers = options.headers || {};
        options.headers['Authorization'] = 'Bearer ' + csrfToken;  // CSRF-Token im Header hinzufügen
    }

    return originalFetch(url, options);  // Führe die Anfrage aus
};
