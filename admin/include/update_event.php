<?php
require 'db.php'; // Deine DB-Verbindung

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Alle Formulardaten aus POST erhalten
    $event_id = $_POST['event_id']; // Event ID aus POST-Daten
    $vorname_nachname = $_POST['vorname_nachname'];
    $telefonnummer = $_POST['telefonnummer'];
    $datum_uhrzeit_event = $_POST['datum_uhrzeit_event']; // Datum und Uhrzeit direkt aus POST
    $ort = $_POST['ort'];
    $event_lead = $_POST['event_lead'];
    $event = $_POST['event']; // Neues Event-Feld
    $anmerkung = $_POST['anmerkung']; // Neues Anmerkung-Feld

    // SQL-Update-Abfrage zum Aktualisieren der Event-Daten
    $sql = "UPDATE eventplanung SET
            vorname_nachname = :vorname_nachname,
            telefonnummer = :telefonnummer,
            datum_uhrzeit_event = :datum_uhrzeit_event,
            ort = :ort,
            event_lead = :event_lead,
            event = :event, 
            anmerkung = :anmerkung
            WHERE id = :event_id"; // 'id' statt 'event_id' in der WHERE-Klausel

    // Datenbankvorbereitung und Bindung der Parameter
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':vorname_nachname', $vorname_nachname);
    $stmt->bindParam(':telefonnummer', $telefonnummer);
    $stmt->bindParam(':datum_uhrzeit_event', $datum_uhrzeit_event);
    $stmt->bindParam(':ort', $ort);
    $stmt->bindParam(':event_lead', $event_lead);
    $stmt->bindParam(':event', $event);
    $stmt->bindParam(':anmerkung', $anmerkung);
    $stmt->bindParam(':event_id', $event_id);

    // Daten speichern und Antwort zurückgeben
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Änderungen erfolgreich gespeichert']);
    } else {
        // Fehlerausgabe, falls das Update fehlschlägt
        echo json_encode(['message' => 'Fehler beim Speichern der Änderungen', 'error' => $stmt->errorInfo()]);
    }
}
?>
