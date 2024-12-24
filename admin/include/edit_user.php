<?php
// Verbindung und Sitzung starten
include 'db.php';
session_start();

// Überprüfen, ob die Anfrage korrekt ist
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
    exit;
}

try {
    // Pflichtfeld überprüfen
    $user_id = $_POST['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    // Berechtigungen überprüfen und Updates vorbereiten
    $updates = [];

    // Gekündigt-Status aktualisieren
    if ($_SESSION['permissions']['edit_gekündigt'] ?? false) {
        // Hier wird der Wert immer verarbeitet, selbst wenn keine weiteren Felder geändert werden
        $updates['gekündigt'] = isset($_POST['gekündigt']) && $_POST['gekündigt'] === '1' ? 1 : 0;
    }

    // Weitere Felder aktualisieren
    if ($_SESSION['permissions']['edit_name'] ?? false && isset($_POST['name'])) {
        $updates['name'] = $_POST['name'];
    }
    if ($_SESSION['permissions']['edit_nummer'] ?? false && isset($_POST['nummer'])) {
        $updates['nummer'] = $_POST['nummer'];
    }
    if ($_SESSION['permissions']['edit_email'] ?? false && isset($_POST['email'])) {
        $updates['email'] = $_POST['email'];
    }
    if ($_SESSION['permissions']['edit_umail'] ?? false && isset($_POST['umail'])) {
        $updates['umail'] = $_POST['umail'];
    }
    if ($_SESSION['permissions']['edit_kontonummer'] ?? false && isset($_POST['kontonummer'])) {
        $updates['kontonummer'] = $_POST['kontonummer'];
    }

    // Passwort aktualisieren
    if (!empty($_POST['password']) && ($_SESSION['permissions']['edit_password'] ?? false)) {
        $password = $_POST['password'];

        // Passwort-Validierung
        if (strlen($password) < 8) {
            echo json_encode(['success' => false, 'message' => 'Das Passwort muss mindestens 8 Zeichen lang sein.']);
            exit;
        }

        $updates['password'] = password_hash($password, PASSWORD_BCRYPT);
    }

    // Updates in der Datenbank ausführen
    if (!empty($updates)) {
        $sql = "UPDATE users SET ";
        $params = [];

        foreach ($updates as $key => $value) {
            $sql .= "$key = :$key, ";
            $params[":$key"] = $value;
        }

        $sql = rtrim($sql, ', ') . " WHERE id = :user_id";
        $params[':user_id'] = $user_id;

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['success' => true, 'message' => 'Daten erfolgreich gespeichert.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Keine Änderungen vorgenommen.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    exit;
}
