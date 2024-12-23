<?php
include 'db.php';
session_start();

header('Content-Type: application/json');

// Nur POST-Anfragen akzeptieren
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    // Felder überprüfen und Berechtigungen prüfen
    $fields = ['name', 'nummer', 'email', 'umail', 'kontonummer'];
    $updates = [];
    $params = ['id' => $user_id];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            // Berechtigung prüfen
            $permission = 'edit_' . $field;
            if ($_SESSION['permissions'][$permission] ?? false) {
                $updates[] = "$field = :$field";
                $params[$field] = $_POST[$field];
            }
        }
    }

    if (empty($updates)) {
        echo json_encode(['success' => false, 'message' => 'Keine Berechtigung zum Bearbeiten.']);
        exit;
    }

    // SQL-Abfrage zusammenstellen und ausführen
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute($params);
        echo json_encode(['success' => true, 'message' => 'Benutzerinformationen erfolgreich gespeichert.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
}
