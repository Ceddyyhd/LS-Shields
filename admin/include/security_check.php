<?php
session_start();

// URL überprüfen
$current_url = $_SERVER['REQUEST_URI']; // Holt die aktuelle URL
$allowed_admin_prefix = '/admin/';  // Erlaubte URL-Präfix für Admin

// Überprüfen, ob die Anfrage vom richtigen Verzeichnis kommt
if (strpos($current_url, $allowed_admin_prefix) !== 0) {
    // Falls nicht, blockiere die Anfrage und leite auf eine Fehlerseite weiter
    header('Location: /error.php');
    exit;
}

// Weitere Sicherheitslogik (wie Authentifizierung) hier, falls notwendig
?>
