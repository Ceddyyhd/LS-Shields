<?php
ob_start(); // Beginnt die Ausgabe-Pufferung, sodass keine Ausgabe vor der Weiterleitung erfolgt.

session_start();

// Sicherstellen, dass nur GET oder POST Anfragen akzeptiert werden
$allowed_methods = ['GET', 'POST'];  // Nur GET und POST erlauben
$request_method = strtoupper($_SERVER['REQUEST_METHOD']); // Holt die Methode der Anfrage und konvertiert sie zu Großbuchstaben

if (!in_array($request_method, $allowed_methods)) {
    // Falls die Anfrage nicht GET oder POST ist, Weiterleitung zur Fehlerseite
    header('Location: /error.php');
    exit;  // Wichtig, dass kein weiterer Code ausgeführt wird!
}

// Weitere Sicherheitslogik (wie Authentifizierung) hier

ob_end_flush(); // Gibt die Pufferung aus, falls keine Weiterleitung durchgeführt wird.
?>
