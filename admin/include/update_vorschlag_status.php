<?php
// Verbindung zur Datenbank einbinden
include 'db.php';
session_start();

// Überprüfen, ob die Anfrage mit den richtigen Parametern gesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null; // ID des Verbesserungsvorschlags
    $action = $_POST['action'] ?? null; // Die durchgeführte Aktion

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

        // Wenn die Aktion 'change_status' ist, setze den Status auf 'In Bearbeitung'
        if ($action === 'change_status' && $vorschlag['status'] === 'Eingetroffen') {
            $newStatus = 'In Bearbeitung';
        } 
        // Wenn die Aktion 'move_to_eventplanung' ist, setze den Status auf 'Abgeschlossen'
        elseif ($action === 'move_to_eventplanung' && $vorschlag['status'] === 'in Bearbeitung') {
            $newStatus = 'Abgeschlossen';
        } else {
            echo json_encode(['success' => false, 'message' => 'Ungültige Aktion für den aktuellen Status.']);
            exit;
        }

        // Den Status in der Datenbank aktualisieren
        $stmt = $conn->prepare("UPDATE verbesserungsvorschlaege SET status = :new_status WHERE id = :id");
        $stmt->execute([':new_status' => $newStatus, ':id' => $id]);

        // Erfolgreiche Antwort zurückgeben
        echo json_encode(['success' => true, 'message' => 'Status erfolgreich aktualisiert.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren des Status: ' . $e->getMessage()]);
    }
}
?>
