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

// Deine weiterführende Logik für CSRF-Überprüfung oder andere Sicherheitschecks
// CSRF-Token Beispiel:
$csrf_token_from_cookie = isset($_COOKIE['csrf_token_public']) ? $_COOKIE['csrf_token_public'] : '';
$csrf_token_from_post = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

if (empty($csrf_token_from_post) || $csrf_token_from_post !== $csrf_token_from_cookie) {
    // CSRF-Token ist ungültig
    echo json_encode(['success' => false, 'message' => 'CSRF Token ungültig.']);
    exit;
}

// Weitere Sicherheitslogik (wie Authentifizierung) hier
?>
