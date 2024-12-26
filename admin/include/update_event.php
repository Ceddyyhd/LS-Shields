<?php
// Verbindung zur Datenbank herstellen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vorname_nachname = $_POST['vorname_nachname'];
    $telefonnummer = $_POST['telefonnummer'];
    $datum_uhrzeit_event = $_POST['datum_uhrzeit_event']; // Neues Feld
    $ort = $_POST['ort'];
    $event_lead = $_POST['event_lead'];

    // Update-Abfrage
    $sql = "UPDATE eventplanung SET
            vorname_nachname = '$vorname_nachname',
            telefonnummer = '$telefonnummer',
            datum_uhrzeit_event = '$datum_uhrzeit_event',  // Neues Feld
            ort = '$ort',
            event_lead = '$event_lead'
            WHERE id = $event_id";

    mysqli_query($conn, $sql);
}
?>
