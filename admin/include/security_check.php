<?php
session_start(); // Sitzung starten

// CSRF-Token aus dem Cookie holen
$csrf_token_from_cookie = isset($_COOKIE['csrf_token_public']) ? $_COOKIE['csrf_token_public'] : '';

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

// Berechne den privaten Token mit der geheimen Prüfziffer (secret key) und dem öffentlichen Token
$private_token_calculated = hash_hmac('sha256', $csrf_token_from_cookie, 'my_very_secret_key');

// Hole den privaten Token aus der Datenbank
require_once 'db.php'; // Deine DB-Verbindung hier
try {
    $stmt = $conn->prepare("SELECT csrf_token_private FROM users WHERE id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Überprüfen, ob der private Token korrekt ist
    if ($private_token_calculated !== $user['csrf_token_private']) {
        echo json_encode(['success' => false, 'message' => 'CSRF Token ungültig.']);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
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
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Füge 'Authorization' hinzu, um den CSRF-Token zu akzeptieren

// Referer-Header-Überprüfung für zusätzliche Sicherheit
if (empty($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'deine-website.com') === false) {
    echo json_encode(['success' => false, 'message' => 'Zugang verweigert.']);
    exit;
}
?>
