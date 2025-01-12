// Sicherheitsüberprüfung, ob der CSRF-Token im Header vorhanden ist
const originalFetch = window.fetch;

window.fetch = function(url, options = {}) {
    if (options.method === 'POST') {
        // Token aus dem Header hinzufügen
        const csrfToken = getCsrfTokenFromCookie();  // Hol den Token direkt aus dem Cookie oder einer sicheren Quelle

        if (!csrfToken) {
            console.error('CSRF Token fehlt!');
            return Promise.reject(new Error('CSRF Token fehlt!'));  // Beende die Anfrage, falls kein Token vorhanden ist
        }

        options.headers = options.headers || {};
        options.headers['Authorization'] = 'Bearer ' + csrfToken;  // CSRF-Token im Header hinzufügen
        return originalFetch(url, options);  // Sende die Anfrage mit dem Token im Header
    } else {
        return originalFetch(url, options);  // Rufe die Original-`fetch`-Methode auf, wenn keine POST-Anfrage
    }
};
