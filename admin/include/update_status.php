<?php
include 'db.php'; // Datenbankverbindung

session_start(); // Session starten, um Benutzerrechte abzurufen

// Überprüfen, ob der POST-Request korrekt ist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
        exit;
    }

    // Überprüfe, ob die action gesetzt ist
    if (!isset($_POST['action'])) {
        echo json_encode(['success' => false, 'error' => 'Keine Aktion angegeben']);
        exit;
    }

    $id = (int) $_POST['id'];   // Anfrage-ID
    $action = $_POST['action'];  // Aktion (change_status oder move_to_eventplanung)

    // Benutzerrechte aus der Session laden
    $permissions = $_SESSION['permissions'] ?? [];

    // Benutzername und user_id aus der Session holen
    $erstellt_von = $_SESSION['username'] ?? 'Unbekannt';
    $user_id = $_SESSION['user_id'] ?? null;

    // Überprüfen, ob die Benutzer-ID in der Session vorhanden ist
    if (!$user_id) {
        echo json_encode(['success' => false, 'error' => 'Benutzer-ID nicht in der Session vorhanden']);
        exit;
    }

    // Überprüfe, ob die Aktion 'change_status' ist
    if ($action === 'change_status') {
        // Überprüfen, ob der Benutzer die Berechtigung hat, den Status zu ändern
        if (!($permissions['change_to_in_bearbeitung'] ?? false)) {
            echo json_encode(['success' => false, 'error' => 'Keine Berechtigung, um den Status in "in Bearbeitung" zu ändern.']);
            exit;
        }

        // Status auf "in Bearbeitung" setzen
        $stmt = $conn->prepare("UPDATE anfragen SET status = 'in Bearbeitung' WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Log-Eintrag für diese Aktion erstellen
        $action_log = "Status auf 'in Bearbeitung' gesetzt";
        $logStmt = $conn->prepare("INSERT INTO anfragen_logs (username, action, anfrage_id) 
                                   VALUES (:username, :action, :id)");
        $logStmt->execute([
            ':username' => $erstellt_von,  // Jetzt wird der Benutzername verwendet
            ':action' => $action_log,
            ':id' => $id
        ]);

        // Allgemeiner Log-Eintrag
        logAction('UPDATE', 'anfragen', 'id: ' . $id . ', status auf "in Bearbeitung" gesetzt von: ' . $_SESSION['user_id']);

        echo json_encode(['success' => true, 'message' => 'Status erfolgreich geändert']);
    } 
    
    // Überprüfe, ob die Aktion 'move_to_eventplanung' ist
    elseif ($action === 'move_to_eventplanung') {
        // Überprüfen, ob der Benutzer die Berechtigung hat, den Status in "in Planung" zu ändern
        if (!($permissions['change_to_in_planung'] ?? false)) {
            echo json_encode(['success' => false, 'error' => 'Keine Berechtigung, um den Status in "in Planung" zu ändern.']);
            exit;
        }

        // Anfrage aus der Tabelle 'anfragen' holen
        $stmt = $conn->prepare("SELECT * FROM anfragen WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $anfrage = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($anfrage) {
            // Anfrage in die Tabelle 'eventplanung' verschieben
            $stmt = $conn->prepare("INSERT INTO eventplanung (vorname_nachname, telefonnummer, anfrage, datum_uhrzeit, status)
                                    VALUES (:vorname_nachname, :telefonnummer, :anfrage, :datum_uhrzeit, 'in Planung')");
            $stmt->execute([
                ':vorname_nachname' => $anfrage['vorname_nachname'],
                ':telefonnummer' => $anfrage['telefonnummer'],
                ':anfrage' => $anfrage['anfrage'],
                ':datum_uhrzeit' => $anfrage['datum_uhrzeit'],
            ]);

            // Log-Eintrag für das Verschieben der Anfrage
            $action_log = "Anfrage in die Eventplanung verschoben";
            $logStmt = $conn->prepare("INSERT INTO anfragen_logs (username, action, anfrage_id) 
                                       VALUES (:username, :action, :id)");
            $logStmt->execute([
                ':username' => $erstellt_von,  // Jetzt wird der Benutzername verwendet
                ':action' => $action_log,
                ':id' => $id
            ]);

            // Anfrage aus der Tabelle 'anfragen' löschen
            $stmt = $conn->prepare("DELETE FROM anfragen WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode(['success' => true, 'removed' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Anfrage nicht gefunden']);
        }
    } 
    
    // Ungültige Aktion
    else {
        echo json_encode(['success' => false, 'error' => 'Ungültige Aktion']);
    }
}

// Funktion zum Loggen von Aktionen
function logAction($action, $table, $details) {
    global $conn;

    // SQL-Abfrage zum Einfügen des Log-Eintrags
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, details, user_id, timestamp) VALUES (:action, :table_name, :details, :user_id, NOW())");
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':table_name', $table, PDO::PARAM_STR);
    $stmt->bindParam(':details', $details, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
}
?>
