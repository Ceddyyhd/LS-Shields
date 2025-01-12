<?php
session_start();

// Sicherstellen, dass nur GET oder POST Anfragen akzeptiert werden
$allowed_methods = ['GET', 'POST', 'Put', 'Delete'];
$request_method = $_SERVER['REQUEST_METHOD']; // Holt die Methode der Anfrage

if (!in_array($request_method, $allowed_methods)) {
    // Falls die Anfrage nicht GET oder POST ist, Weiterleitung zur Fehlerseite
    header('Location: /error.php');
    exit;
}

// Weitere Sicherheitslogik (wie Authentifizierung) hier
?>
