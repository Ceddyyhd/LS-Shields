<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? $_GET['id'] ?? null;  // Benutzerdaten entweder aus POST oder URL holen
    $letzte_spind_kontrolle = $_POST['letzte_spind_kontrolle'] ?? null;
    $notiz = $_POST['notiz'] ?? null;

    // Berechtigungsprüfung
    if (!($_SESSION['permissions']['edit_employee'] ?? false)) {
        echo json_encode(['success' => false, 'message' => 'Keine Berechtigung, Änderungen vorzunehmen.']);
        exit;
    }

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    // Überprüfen, ob Felder leer sind und NULL zuweisen
    if (empty($letzte_spind_kontrolle)) {
        $letzte_spind_kontrolle = null;  // Setze NULL, wenn das Datum leer ist
    }

    if (empty($notiz)) {
        $notiz = null;  // Setze NULL, wenn keine Notiz eingegeben wurde
    }

    try {
        // Überprüfen, ob es bereits einen Eintrag für diesen Benutzer gibt
        $stmt = $conn->prepare("SELECT id FROM spind_kontrolle_notizen WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $existingEntry = $stmt->fetch(PDO::FETCH_ASSOC);

        // Benutzername für das Log
        $stmt = $conn->prepare("SELECT name FROM users WHERE id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $editor_name = $user['name'] ?? 'Unbekannt';

        // Wenn der Eintrag bereits existiert, aktualisieren
        if ($existingEntry) {
            $stmt = $conn->prepare("UPDATE spind_kontrolle_notizen 
                                    SET letzte_spind_kontrolle = :letzte_spind_kontrolle, notizen = :notizen 
                                    WHERE user_id = :user_id");
            $stmt->execute([
                ':letzte_spind_kontrolle' => $letzte_spind_kontrolle,
                ':notizen' => $notiz,
                ':user_id' => $user_id
            ]);

            // Log für die Änderung
            $stmt = $conn->prepare("INSERT INTO spind_kontrolle_logs (user_id, editor_name, action) 
                                    VALUES (:user_id, :editor_name, 'Aktualisiert')");
            $stmt->execute([
                ':user_id' => $user_id,
                ':editor_name' => $editor_name
            ]);

        } else {
            // Neuen Eintrag in die Tabelle einfügen
            $stmt = $conn->prepare("INSERT INTO spind_kontrolle_notizen (user_id, letzte_spind_kontrolle, notizen) 
                                    VALUES (:user_id, :letzte_spind_kontrolle, :notizen)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':letzte_spind_kontrolle' => $letzte_spind_kontrolle,
                ':notizen' => $notiz
            ]);

            // Log für die Erstellung
            $stmt = $conn->prepare("INSERT INTO spind_kontrolle_logs (user_id, editor_name, action) 
                                    VALUES (:user_id, :editor_name, 'Erstellt')");
            $stmt->execute([
                ':user_id' => $user_id,
                ':editor_name' => $editor_name
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Änderungen gespeichert.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
    exit;
}
?>
