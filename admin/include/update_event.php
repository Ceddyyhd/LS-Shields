<?php
require 'db.php'; // Deine DB-Verbindung

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Event-ID aus den POST-Daten holen
    $event_id = $_POST['event_id']; // Event ID aus POST-Daten (unverändert)

    // Formulardaten aus POST erhalten
    $vorname_nachname = $_POST['vorname_nachname'];
    $telefonnummer = $_POST['telefonnummer'];
    $datum_uhrzeit_event = $_POST['datum_uhrzeit_event'];
    $ort = $_POST['ort'];
    $event_lead = $_POST['event_lead'];

    // Update-Abfrage ausführen
    $sql = "UPDATE eventplanung SET
            vorname_nachname = :vorname_nachname,
            telefonnummer = :telefonnummer,
            datum_uhrzeit_event = :datum_uhrzeit_event,
            ort = :ort,
            event_lead = :event_lead
            WHERE id = :event_id"; // 'id' statt 'event_id' in der WHERE-Klausel

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':vorname_nachname', $vorname_nachname);
    $stmt->bindParam(':telefonnummer', $telefonnummer);
    $stmt->bindParam(':datum_uhrzeit_event', $datum_uhrzeit_event);
    $stmt->bindParam(':ort', $ort);
    $stmt->bindParam(':event_lead', $event_lead);
    $stmt->bindParam(':event_id', $event_id);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Änderungen erfolgreich gespeichert']);
    } else {
        echo json_encode(['message' => 'Fehler beim Speichern der Änderungen']);
    }
}
?>
