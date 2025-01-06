<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Benutzerdaten entweder aus POST oder URL holen
    $user_id = $_POST['user_id'] ?? $_GET['id'] ?? null;
    $letzte_spind_kontrolle = $_POST['letzte_spind_kontrolle'] ?? null;
    $notiz = $_POST['notiz'] ?? null;
    $ausruestung = json_decode($_POST['ausruestung'], true); // JSON-Daten in ein Array umwandeln

    // Berechtigungsprüfung
    if (!($_SESSION['permissions']['edit_employee'] ?? false)) {
        echo json_encode(['success' => false, 'message' => 'Keine Berechtigung, Änderungen vorzunehmen.']);
        exit;
    }

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    try {
        // Benutzername für das Log
        $stmt = $conn->prepare("SELECT name FROM users WHERE id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $editor_name = $user ? $user['name'] : 'Unbekannt'; // Falls keine Daten vorhanden sind

        // Bestandsänderungen und Historie
        foreach ($ausruestung as $key_name => $status) {
            // Bestandsänderung um 1 je nachdem, ob die Checkbox aktiviert oder deaktiviert wird
            $stmt = $conn->prepare("SELECT * FROM ausruestungstypen WHERE key_name = :key_name");
            $stmt->execute([':key_name' => $key_name]);
            $ausruestungItem = $stmt->fetch(PDO::FETCH_ASSOC);

            $new_stock = $ausruestungItem['stock'] + ($status == 1 ? -1 : 1); // Bestand anpassen

            // Bestandsänderung speichern
            $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = :stock WHERE key_name = :key_name");
            $stmt->execute([
                ':stock' => $new_stock,
                ':key_name' => $key_name
            ]);

            // Historie der Bestandsänderung speichern
            $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) 
                                    VALUES (:user_id, :key_name, :action, :stock_change, :editor_name)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':key_name' => $key_name,
                ':action' => ($status == 1 ? 'Ausgegeben' : 'Zurückgegeben'),
                ':stock_change' => ($status == 1 ? -1 : 1),
                ':editor_name' => $editor_name
            ]);
        }

        // Spindkontrolle und Notizen speichern
        $stmt = $conn->prepare("SELECT id FROM spind_kontrolle_notizen WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $existingEntry = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingEntry) {
            $stmt = $conn->prepare("UPDATE spind_kontrolle_notizen 
                                    SET letzte_spind_kontrolle = :letzte_spind_kontrolle, notizen = :notizen 
                                    WHERE user_id = :user_id");
            $stmt->execute([
                ':letzte_spind_kontrolle' => $letzte_spind_kontrolle,
                ':notizen' => $notiz,
                ':user_id' => $user_id
            ]);
        } else {
            $stmt = $conn->prepare("INSERT INTO spind_kontrolle_notizen (user_id, letzte_spind_kontrolle, notizen) 
                                    VALUES (:user_id, :letzte_spind_kontrolle, :notizen)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':letzte_spind_kontrolle' => $letzte_spind_kontrolle,
                ':notizen' => $notiz
            ]);
        }

        // Log für die Änderung oder Erstellung
        $stmt = $conn->prepare("INSERT INTO spind_kontrolle_logs (user_id, editor_name, action) 
                                VALUES (:user_id, :editor_name, :action)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':editor_name' => $editor_name,
            ':action' => 'Änderungen gespeichert'
        ]);

        echo json_encode(['success' => true, 'message' => 'Änderungen gespeichert.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
    exit;
}
