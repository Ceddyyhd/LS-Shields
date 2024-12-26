<?php
// update_event_ajax.php
require 'db.php'; // Deine DB-Verbindung

// Empfange die Daten vom AJAX-Request
$vorname_nachname = $_POST['vorname_nachname'];
$telefonnummer = $_POST['telefonnummer'];
$datum_uhrzeit_event = $_POST['datum_uhrzeit_event'];
$ort = $_POST['ort'];
$event_lead = $_POST['event_lead'];
$event_id = $_POST['event_id']; // Falls die Event-ID übergeben wird

// Update-Abfrage ausführen
$sql = "UPDATE eventplanung SET
        vorname_nachname = '$vorname_nachname',
        telefonnummer = '$telefonnummer',
        datum_uhrzeit_event = '$datum_uhrzeit_event',
        ort = '$ort',
        event_lead = '$event_lead'
        WHERE id = $event_id";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['message' => 'Änderungen erfolgreich gespeichert']);
} else {
    echo json_encode(['message' => 'Fehler beim Speichern der Änderungen']);
}
?>
