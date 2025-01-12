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
    const csrfToken = getCsrfTokenFromCookie(); // Hol den Token direkt aus dem Cookie

    if (csrfToken) {
        // Füge den CSRF-Token in den Header für alle Methoden ein, einschließlich GET
        options.headers = options.headers || {};
        options.headers['csrf_token'] = csrfToken;  // CSRF-Token als Header hinzufügen
    }

    return originalFetch(url, options);
};
