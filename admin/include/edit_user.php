<?php
// Verbindung und Sitzung starten
include 'db.php';
session_start();

// Überprüfen, ob die Anfrage korrekt ist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    // Berechtigungen überprüfen und Updates vorbereiten
    $updates = [];
    if ($_SESSION['permissions']['edit_name'] ?? false) {
        $updates['name'] = $_POST['name'] ?? '';
    }
    if ($_SESSION['permissions']['edit_nummer'] ?? false) {
        $updates['nummer'] = $_POST['nummer'] ?? '';
    }
    if ($_SESSION['permissions']['edit_email'] ?? false) {
        $updates['email'] = $_POST['email'] ?? '';
    }
    if ($_SESSION['permissions']['edit_umail'] ?? false) {
        $updates['umail'] = $_POST['umail'] ?? '';
    }
    if ($_SESSION['permissions']['edit_kontonummer'] ?? false) {
        $updates['kontonummer'] = $_POST['kontonummer'] ?? '';
    }

    // Passwort verarbeiten, falls erlaubt und übergeben
    if (isset($updates['password'])) {
        if (empty($updates['password'])) {
            echo json_encode(['success' => false, 'message' => 'Passwort darf nicht leer sein.']);
            exit;
        }
    
        // Passwort-Hashing
        $updates['password'] = password_hash($updates['password'], PASSWORD_BCRYPT);
    } else {
        unset($updates['password']); // Passwort-Update entfernen, wenn Checkbox nicht aktiv war
    }

    // Daten aktualisieren
    if (!empty($updates)) {
        $sql = "UPDATE users SET ";
        $params = [];
        foreach ($updates as $key => $value) {
            $sql .= "$key = :$key, ";
            $params[":$key"] = $value;
        }
        $sql = rtrim($sql, ', ') . " WHERE id = :user_id";
        $params[':user_id'] = $user_id;

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['success' => true, 'message' => 'Daten erfolgreich gespeichert.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Keine Änderungen vorgenommen.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
}
