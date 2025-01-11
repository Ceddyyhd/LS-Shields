<?php
require_once 'db.php'; // Verbindung zur Datenbank
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // Event-Daten abfragen (Tabelle 'eventplanung' verwenden)
    $query = "SELECT * FROM eventplanung WHERE id = :event_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($event) {
        // Event duplizieren (alle Felder außer 'event_lead' und 'team_verteilung', Status auf 'in Bearbeitung' setzen)
        $insertQuery = "
            INSERT INTO eventplanung (vorname_nachname, telefonnummer, anfrage, datum_uhrzeit, status, summernote_content, datum_uhrzeit_event, ort, event, anmerkung)
            VALUES (:vorname_nachname, :telefonnummer, :anfrage, :datum_uhrzeit, 'in Planung', :summernote_content, :datum_uhrzeit_event, :ort, :event, :anmerkung)";

        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bindParam(':vorname_nachname', $event['vorname_nachname']);
        $insertStmt->bindParam(':telefonnummer', $event['telefonnummer']);
        $insertStmt->bindParam(':anfrage', $event['anfrage']);
        $insertStmt->bindParam(':datum_uhrzeit', $event['datum_uhrzeit']);
        $insertStmt->bindParam(':summernote_content', $event['summernote_content']);
        $insertStmt->bindParam(':datum_uhrzeit_event', $event['datum_uhrzeit_event']);
        $insertStmt->bindParam(':ort', $event['ort']);
        $insertStmt->bindParam(':event', $event['event']);
        $insertStmt->bindParam(':anmerkung', $event['anmerkung']);
        
        if ($insertStmt->execute()) {
            // Log-Eintrag für das Duplizieren
            $new_event_id = $conn->lastInsertId();
            logAction('DUPLICATE', 'eventplanung', 'event_id: ' . $new_event_id . ', duplicated_by: ' . $_SESSION['user_id']);

            echo json_encode(['success' => true, 'message' => 'Event erfolgreich dupliziert']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Duplizieren des Events']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Event nicht gefunden']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine Event-ID übergeben']);
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
