<?php
session_start(); // Sitzung starten

// Sicherstellen, dass es eine POST-Anfrage ist
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage-Methode.']);
    exit;
}

// Öffentlichen Token aus dem Header holen
$headers = getallheaders();
$public_token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

// Sicherstellen, dass der öffentliche Token vorhanden ist
if (empty($public_token)) {
    echo json_encode(['success' => false, 'message' => 'Öffentlicher Token fehlt!']);
    exit;
}

// Hier holst du den privaten Token aus der Datenbank (dieser sollte mit dem öffentlichen Token verknüpft sein)
require_once 'db.php'; // Datenbankverbindung
$query = "SELECT private_token FROM csrf_tokens WHERE public_token = :public_token LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':public_token', $public_token);
$stmt->execute();

$private_token = $stmt->fetchColumn(); // Der private Token, der in der Datenbank gespeichert ist

// Wenn der private Token nicht gefunden wurde, ist die Anfrage ungültig
if (!$private_token) {
    echo json_encode(['success' => false, 'message' => 'Ungültiger öffentlicher Token.']);
    exit;
}

// CSRF-Token aus dem Cookie holen (falls notwendig)
$csrf_token_from_cookie = isset($_COOKIE['csrf_token']) ? $_COOKIE['csrf_token'] : '';

// Überprüfen, ob der öffentliche Token mit dem privaten Token übereinstimmt
if ($public_token !== $private_token) {
    echo json_encode(['success' => false, 'message' => 'CSRF Token ungültig.']);
    exit;
}

// Weitere Eingabewerte validieren: Alle Eingabewerte aus dem POST-Array werden validiert und saniert
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
