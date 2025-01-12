<?php
session_start();

// Überprüfen, ob es eine POST- oder GET-Anfrage ist
$allowed_methods = ['GET', 'POST'];  // Nur GET und POST erlauben
$request_method = strtoupper($_SERVER['REQUEST_METHOD']); // Methode der Anfrage in Großbuchstaben

// Nur POST oder GET werden akzeptiert
if (!in_array($request_method, $allowed_methods)) {
    header('Location: /error.php');
    exit;
}

// Sicherstellen, dass die Anfrage von /admin/ kommt (einschließlich Unterverzeichnissen wie /admin/include/)
$current_url = $_SERVER['REQUEST_URI'];

// Wenn die URL nicht mit /admin/ beginnt, leite zur Fehlerseite weiter
if (strpos($current_url, '/admin/') !== 0) {
    header('Location: /error.php');
    exit;
}

// CSRF-Token-Überprüfung und andere Sicherheitsprüfungen
// Weitere Logik für Sicherheit

?>
