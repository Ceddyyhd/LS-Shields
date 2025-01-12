// Holen des CSRF-Tokens aus dem Meta-Tag
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// CSRF-Token Interceptor für alle fetch-Anfragen
const originalFetch = window.fetch;

window.fetch = function(url, options = {}) {
    // Füge den CSRF-Token zu allen POST-Anfragen hinzu
    if (options.method === 'POST') {
        options.headers = options.headers || {};
        options.headers['Authorization'] = 'Bearer ' + csrfToken; // CSRF-Token im Header hinzufügen
    }

    // Rufe die Original-`fetch`-Methode auf
    return originalFetch(url, options);
};
