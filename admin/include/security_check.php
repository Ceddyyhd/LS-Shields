<?php
session_start();

// Sicherstellen, dass nur GET oder POST Anfragen akzeptiert werden
$allowed_methods = ['GET', 'POST'];  // Nur GET und POST erlauben
$request_method = strtoupper($_SERVER['REQUEST_METHOD']); // Holt die Methode der Anfrage und konvertiert sie zu Großbuchstaben

if (!in_array($request_method, $allowed_methods)) {
    // Falls die Anfrage nicht GET oder POST ist, Weiterleitung zur Fehlerseite
    header('Location: /error.php');
    exit;
}

// Weitere Sicherheitslogik (wie Authentifizierung) hier
?>
