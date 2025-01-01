<?php
include 'db.php'; // Datenbankverbindung

session_start(); // Session starten, um Benutzerrechte abzurufen

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_POST['id'];
    $action = $_POST['action'] ?? '';

    // Benutzerrechte aus der Session laden
    $permissions = $_SESSION['permissions'] ?? [];

    // Benutzername aus den POST-Daten holen (erstellt_von)
    $erstellt_von = $_POST['erstellt_von'] ?? 'Unbekannt';

    // Rechteprüfung: Status "in Bearbeitung" ändern
    if ($action === 'change_status') {
        if (!($permissions['change_to_in_bearbeitung'] ?? false)) {
            echo json_encode(['success' => false, 'error' => 'Keine Berechtigung, um den Status in "in Bearbeitung" zu ändern.']);
            exit;
        }

        // Status auf "in Bearbeitung" setzen
        $stmt = $conn->prepare("UPDATE anfragen SET status = 'in Bearbeitung' WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Log-Eintrag für diese Aktion erstellen
        $action_log = "Status auf 'in Bearbeitung' gesetzt";
        $logStmt = $conn->prepare("INSERT INTO anfragen_logs (user_id, action, anfrage_id) 
                                   VALUES ((SELECT id FROM users WHERE username = :erstellt_von), :action, :id)");
        $logStmt->execute([
            ':erstellt_von' => $erstellt_von,
            ':action' => $action_log,
            ':id' => $id
        ]);

        echo json_encode(['success' => true, 'new_status' => 'in Bearbeitung']);
    } 
    
    // Rechteprüfung: Anfrage in eventplanung verschieben
    elseif ($action === 'move_to_eventplanung') {
        if (!($permissions['change_to_in_planung'] ?? false)) {
            echo json_encode(['success' => false, 'error' => 'Keine Berechtigung, um den Status in "in Planung" zu ändern.']);
            exit;
        }

        // Anfrage aus Tabelle `anfragen` holen
        $stmt = $conn->prepare("SELECT * FROM anfragen WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $anfrage = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($anfrage) {
            // Anfrage in Tabelle `eventplanung` einfügen
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
            $logStmt = $conn->prepare("INSERT INTO anfragen_logs (user_id, action, anfrage_id) 
                                       VALUES ((SELECT id FROM users WHERE username = :erstellt_von), :action, :id)");
            $logStmt->execute([
                ':erstellt_von' => $erstellt_von,
                ':action' => $action_log,
                ':id' => $id
            ]);

            // Anfrage aus Tabelle `anfragen` löschen
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
?>
