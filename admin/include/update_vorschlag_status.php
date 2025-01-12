<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

// Verbindung zur Datenbank einbinden
include 'db.php';
session_start();

// Überprüfen, ob die Anfrage mit den richtigen Parametern gesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null; // ID des Verbesserungsvorschlags
    $action = $_POST['action'] ?? null; // Die durchgeführte Aktion

    // Überprüfen, ob id und action übergeben wurden
    if (!$id || !$action) {
        echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
        exit;
    }

    try {
        // SQL-Abfrage, um den aktuellen Status des Vorschlags zu überprüfen
        $stmt = $conn->prepare("SELECT status FROM verbesserungsvorschlaege WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $vorschlag = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vorschlag) {
            echo json_encode(['success' => false, 'message' => 'Vorschlag nicht gefunden.']);
            exit;
        }

        // Statusänderung basierend auf der Aktion
        if ($action === 'change_status' && $vorschlag['status'] === 'Eingetroffen') {
            $newStatus = 'in Bearbeitung';
        } 
        // Den Status auf "Abgeschlossen" ändern
        elseif ($action === 'move_to_eventplanung' && $vorschlag['status'] === 'in Bearbeitung') {
            $newStatus = 'Abgeschlossen';
        } else {
            echo json_encode(['success' => false, 'message' => 'Ungültige Aktion für den aktuellen Status.']);
            exit;
        }

        // Fehlerbehandlung und Debugging - Zeigt das SQL-Statement und die Parameter an
        error_log("UPDATE verbesserungsvorschlaege SET status = :new_status WHERE id = :id");
        error_log("Parameters: new_status = $newStatus, id = $id");

        // Den Status in der Datenbank aktualisieren
        $stmt = $conn->prepare("UPDATE verbesserungsvorschlaege SET status = :new_status WHERE id = :id");
        $stmt->execute([':new_status' => $newStatus, ':id' => $id]);

        // Überprüfen, ob die Zeilenanzahl durch das UPDATE geändert wurde
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'new_status' => $newStatus, 'message' => 'Status erfolgreich aktualisiert.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Keine Änderungen in der Datenbank vorgenommen.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren des Status: ' . $e->getMessage()]);
    }
}
?>
