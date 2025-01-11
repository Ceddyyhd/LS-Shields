<?php
// security_check.php

session_start(); // Sitzung starten

// CSRF Token Schutz: Token aus der Session holen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'CSRF Token ungültig.']);
        exit;
    }
}

// Sicherstellen, dass es eine POST-Anfrage ist
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage-Methode.']);
    exit;
}

// Eingabewerte validieren: Alle Eingabewerte aus dem POST-Array werden validiert und saniert
function sanitize_input($data) {
    return filter_var($data, FILTER_SANITIZE_STRING); // Für Texte
}

foreach ($_POST as $key => $value) {
    if (is_string($value)) {
        $_POST[$key] = sanitize_input($value);
    }
}

// Cross-Origin Resource Sharing (CORS) Header für sicheren Zugriff
header('Access-Control-Allow-Origin: https://deine-website.com');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Referer-Header-Überprüfung für zusätzliche Sicherheit
if (empty($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'deine-website.com') === false) {
    echo json_encode(['success' => false, 'message' => 'Zugang verweigert.']);
    exit;
}
?>
