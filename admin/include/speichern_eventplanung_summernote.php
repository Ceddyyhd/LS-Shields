<?php
// Fehler anzeigen (nur zu Debugging-Zwecken)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('db.php'); // Datenbankverbindung einbinden

if (isset($_POST['summernoteContent'])) {
    // Sicherstellen, dass die Daten vorhanden und korrekt sind
    $summernoteContent = $mysqli->real_escape_string($_POST['summernoteContent']);
    
    // SQL-Abfrage zum Speichern des Inhalts
    $sql = "INSERT INTO Eventplanung (summernote_content) VALUES ('$summernoteContent')";

    // Fehlerbehandlung
    if ($mysqli->query($sql) === TRUE) {
        echo "Daten wurden erfolgreich gespeichert!";
    } else {
        echo "Fehler beim Speichern der Daten: " . $mysqli->error;
    }
} else {
    echo "Fehlende Daten!";
}

$mysqli->close(); // Verbindung schließen
?>
