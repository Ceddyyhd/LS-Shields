<?php

session_start();

// CSRF-Token aus dem Cookie holen
$csrf_token_from_cookie = isset($_COOKIE['csrf_token_public']) ? $_COOKIE['csrf_token_public'] : '';

// Überprüfen, ob es eine POST-Anfrage ist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF-Token aus den POST-Daten holen
    $csrf_token_from_post = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    // Überprüfen, ob der Token im POST und im Cookie übereinstimmen
    if (empty($csrf_token_from_post) || $csrf_token_from_post !== $csrf_token_from_cookie) {
        echo json_encode(['success' => false, 'message' => 'CSRF Token ungültig.']);
        exit;
    }

    // Berechne den privaten Token mit der geheimen Prüfziffer (secret key) und dem öffentlichen Token
    $private_token_calculated = hash_hmac('sha256', $csrf_token_from_cookie, 'my_very_secret_key');

    // Hier wird keine DB mehr benötigt, da der private Token im Cookie gespeichert und verglichen wird
    if ($private_token_calculated !== $_SESSION['csrf_token_private']) {
        echo json_encode(['success' => false, 'message' => 'CSRF Token ungültig.']);
        exit;
    }
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
