<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
    exit;
}

try {
    // Benutzerdaten abrufen
    $userId = $_POST['user_id'] ?? null;
    $name = $_POST['name'] ?? null;
    $nummer = $_POST['nummer'] ?? null;
    $email = $_POST['email'] ?? null;
    $umail = $_POST['umail'] ?? null;
    $kontonummer = $_POST['kontonummer'] ?? null;
    $password = $_POST['password'] ?? null;
    $gekündigt = isset($_POST['gekündigt']) ? 1 : 0; // Checkbox-Wert: 1 = true, 0 = false

    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    // Datenbank-Update vorbereiten
    $fields = [
        'name' => $name,
        'nummer' => $nummer,
        'email' => $email,
        'umail' => $umail,
        'kontonummer' => $kontonummer,
        'gekündigt' => $gekündigt,
    ];

    // Passwort-Hash aktualisieren, falls ein neues Passwort angegeben wurde
    if (!empty($password)) {
        $fields['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    // SQL-Update dynamisch erstellen
    $setFields = [];
    $params = [];
    foreach ($fields as $key => $value) {
        if ($value !== null) {
            $setFields[] = "$key = :$key";
            $params[":$key"] = $value;
        }
    }

    // Benutzer aktualisieren
    $params[':user_id'] = $userId;
    $sql = "UPDATE users SET " . implode(', ', $setFields) . " WHERE id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'Benutzer erfolgreich aktualisiert.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
}
