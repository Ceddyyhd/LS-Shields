// Funktion, um den CSRF-Token direkt aus dem Cookie zu holen
function getCsrfTokenFromCookie() {
    const cookies = document.cookie.split(';');
    console.log('Cookies:', cookies);  // Logge alle Cookies zur Überprüfung
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].trim();
        if (cookie.startsWith('csrf_token=')) {
            const token = cookie.substring('csrf_token='.length);
            console.log('CSRF Token aus Cookie:', token); // Logge den Token aus dem Cookie
            return token;  // Gib den Token zurück
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

        options.headers = options.headers || {};
        options.headers['Authorization'] = 'Bearer ' + csrfToken;  // CSRF-Token im Header hinzufügen
        console.log('Token im Header:', options.headers['Authorization']);  // Logge den Token, der im Header gesendet wird

        return originalFetch(url, options);  // Sende die Anfrage mit dem Token im Header
    } else {
        return originalFetch(url, options);  // Rufe die Original-`fetch`-Methode auf, wenn keine POST-Anfrage
    }
};
