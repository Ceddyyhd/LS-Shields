<?php
session_start(); // Sitzung starten

// CSRF-Token aus dem Cookie holen
$csrf_token_from_cookie = isset($_COOKIE['csrf_token']) ? $_COOKIE['csrf_token'] : '';

// Sicherstellen, dass es eine POST-Anfrage ist
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage-Methode.']);
    exit;
}

// Überprüfen, ob der Token aus dem Header der Anfrage kommt
$headers = getallheaders();
$csrf_token_from_header = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

// Überprüfen, ob der Token im Header und im Cookie übereinstimmt
if (empty($csrf_token_from_header) || $csrf_token_from_header !== $csrf_token_from_cookie) {
    echo json_encode(['success' => false, 'message' => 'CSRF Token ungültig.']);
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
header('Access-Control-Allow-Origin: https://ls-shields.ceddyyhd2.eu');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Füge 'Authorization' hinzu, um den CSRF-Token zu akzeptieren

// Referer-Header-Überprüfung für zusätzliche Sicherheit
if (empty($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'ls-shields.ceddyyhd2.eu') === false) {
    echo json_encode(['success' => false, 'message' => 'Zugang verweigert.']);
    exit;
}
?>
