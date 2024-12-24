<?php
// Verbindung und Sitzung starten
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
    exit;
}

try {
    $user_id = $_POST['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    $updates = [];

    // Gekündigt-Status
    if (isset($_POST['gekündigt'])) {
        $updates['gekündigt'] = $_POST['gekündigt'] === '1' ? 1 : 0;
    }

    // Weitere Felder
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

    // Passwort
    if (!empty($_POST['password']) && ($_SESSION['permissions']['edit_password'] ?? false)) {
        $updates['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
    }

    // SQL erstellen und Parameter binden
    if (!empty($updates)) {
        $sql = "UPDATE users SET ";
        $params = [];
        foreach ($updates as $key => $value) {
            $sql .= "$key = :$key, ";
            $params[":$key"] = $value;
        }
        $sql = rtrim($sql, ', ') . " WHERE id = :id";
        $params[':id'] = $user_id;

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['success' => true, 'message' => 'Daten erfolgreich gespeichert.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Keine Änderungen vorgenommen.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
}
