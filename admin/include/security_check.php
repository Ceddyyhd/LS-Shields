<?php
session_start();

// CSRF-Token aus dem Cookie holen
$csrf_token_from_cookie = isset($_COOKIE['csrf_token_public']) ? $_COOKIE['csrf_token_public'] : '';

// Debugging-Ausgabe
error_log("CSRF Token aus Cookie: " . $csrf_token_from_cookie);

// Sicherstellen, dass es eine POST-Anfrage ist
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage-Methode.']);
    exit;
}

// Überprüfen, ob der Token aus dem Header der Anfrage kommt
$headers = getallheaders();
$csrf_token_from_header = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

// Debugging-Ausgabe
error_log("CSRF Token aus Header: " . $csrf_token_from_header);

// Überprüfen, ob der Token im Header und im Cookie übereinstimmt
if (empty($csrf_token_from_header) || $csrf_token_from_header !== $csrf_token_from_cookie) {
    echo json_encode(['success' => false, 'message' => 'CSRF Token ungültig.']);
    exit;
}

// Berechne den privaten Token mit der geheimen Prüfziffer (secret key) und dem öffentlichen Token
$private_token_calculated = hash_hmac('sha256', $csrf_token_from_cookie, 'my_very_secret_key');

// Überprüfen, ob der private Token aus der Session übereinstimmt
if ($private_token_calculated !== $_SESSION['csrf_token_private']) {
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

?>
