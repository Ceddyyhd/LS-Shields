<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

    // Berechtigungen für 'edit_gekundigt' prüfen und den "gekündigt"-Status setzen
    if (isset($_POST['gekundigt']) && ($_SESSION['permissions']['edit_gekundigt'] ?? false)) {
        $updates['gekündigt'] = ($_POST['gekundigt'] === 'on') ? 'gekündigt' : 'no_kuendigung';
    }

    // Passwort verarbeiten, falls erlaubt und übergeben
    if (isset($_POST['password']) && ($_SESSION['permissions']['edit_password'] ?? false)) {
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

    // Wenn Updates existieren, SQL-Abfrage erstellen und ausführen
    if (!empty($updates)) {
        $sql = "UPDATE users SET ";
        $params = [];

        // Dynamische Erstellung der SQL-Klausel und des Parameter-Arrays
        foreach ($updates as $key => $value) {
            $sql .= "$key = :$key, ";  // Platzhalter wird hinzugefügt
            $params[":$key"] = $value;  // Parameter wird zugewiesen
        }

        // Entferne das letzte Komma und füge die WHERE-Klausel hinzu
        $sql = rtrim($sql, ', ') . " WHERE id = :user_id";
        $params[':user_id'] = $user_id;  // :user_id auch im Array hinzufügen

        // Debugging-Ausgabe (optional, für das Testen)
        // echo "SQL: $sql\n";
        // print_r($params);

        try {
            // Deine SQL-Abfrage ausführen
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['success' => true, 'message' => 'Daten erfolgreich gespeichert.']);
        } catch (PDOException $e) {
            // Fehlerbehandlung: Nur JSON zurückgeben
            echo json_encode([
                'success' => false,
                'message' => 'Fehler beim Speichern: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Keine Änderungen vorgenommen.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
}
?>
