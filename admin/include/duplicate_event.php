<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

require_once 'db.php'; // Verbindung zur Datenbank

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
            echo 'Event duplicated successfully.';
        } else {
            echo 'Failed to duplicate event.';
        }
    } else {
        echo 'Event not found.';
    }
}
?>
