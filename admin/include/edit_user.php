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
    if (isset($_POST['password']) && $_SESSION['permissions']['edit_password'] ?? false) {
        $password = $_POST['password'];

        // Überprüfen, ob das Passwort leer ist, obwohl die Checkbox aktiviert wurde
        if (empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Passwort darf nicht leer sein.']);
            exit;
        }

        // Passwort hashen und hinzufügen
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $updates['password'] = $hashedPassword;
    }

// Gekündigt und Bewerber verarbeiten
$gekuendigt = isset($_POST['gekuendigt']) && $_POST['gekuendigt'] === 'on' ? 'gekuendigt' : 'no_kuendigung';
$bewerber = isset($_POST['bewerber']) && $_POST['bewerber'] === 'on' ? 'ja' : 'nein';

// admin_bereich je nach Bewerberstatus setzen
$admin_bereich = ($bewerber === 'nein') ? 1 : 0;  // Wenn Bewerber 'nein', setze admin_bereich auf 1, andernfalls auf 0

// Updates vorbereiten
$updates['gekuendigt'] = $gekuendigt;  // Den Gekuendigt-Wert in das Updates-Array einfügen
$updates['bewerber'] = $bewerber;  // Den Bewerber-Wert in das Updates-Array einfügen
$updates['admin_bereich'] = $admin_bereich;  // Den admin_bereich-Wert in das Updates-Array einfügen

    // Daten aktualisieren
    if (!empty($updates)) {
        $sql = "UPDATE users SET ";
        $params = [];
        foreach ($updates as $key => $value) {
            $sql .= "$key = :$key, ";
            $params[":$key"] = $value;  // Parameter in die $params-Array einfügen
        }
        $sql = rtrim($sql, ', ') . " WHERE id = :user_id";  // Entferne das letzte Komma
        $params[':user_id'] = $user_id;  // Benutzer-ID hinzufügen

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);  // SQL mit den richtigen Parametern ausführen
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
?>
