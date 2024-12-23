<?php
include 'db.php';
session_start();

header('Content-Type: application/json');

// Prüfen, ob die Anfrage per POST erfolgt ist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    // Felder, die bearbeitet werden können
    $fields = ['name', 'nummer', 'email', 'umail', 'kontonummer'];
    $updates = [];

    // Berechtigungen prüfen und Felder sammeln
    foreach ($fields as $field) {
        if (isset($_POST[$field]) && ($_SESSION['permissions']["edit_$field"] ?? false)) {
            $updates[$field] = $_POST[$field];
        }
    }

    if (empty($updates)) {
        echo json_encode(['success' => false, 'message' => 'Keine Änderungen oder keine Berechtigung.']);
        exit;
    }

    // Update SQL erstellen
    $setClause = [];
    foreach ($updates as $field => $value) {
        $setClause[] = "$field = :$field";
    }

    $sql = "UPDATE users SET " . implode(', ', $setClause) . " WHERE id = :user_id";
    $stmt = $conn->prepare($sql);

    // Bind-Werte vorbereiten
    foreach ($updates as $field => $value) {
        $stmt->bindValue(":$field", $value);
    }
    $stmt->bindValue(":user_id", $user_id);

    // Ausführen
    try {
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Änderungen erfolgreich gespeichert.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
    exit;
}
