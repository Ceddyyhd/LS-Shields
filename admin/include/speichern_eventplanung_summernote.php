<?php
// Einbinden der Datenbankverbindung
include('db.php');

// Überprüfen, ob der Summernote-Inhalt gesendet wurde
if (isset($_POST['summernoteContent'])) {
    // Den Inhalt von Summernote abholen
    $summernoteContent = $mysqli->real_escape_string($_POST['summernoteContent']);

    // SQL-Abfrage zum Speichern des Inhalts
    $sql = "INSERT INTO eventplanung (summernote_content) VALUES ('$summernoteContent')";

    // Überprüfen, ob die Abfrage erfolgreich war
    if ($mysqli->query($sql) === TRUE) {
        echo "Daten wurden erfolgreich gespeichert!";
    } else {
        echo "Fehler beim Speichern der Daten: " . $mysqli->error;
    }
}

// Verbindung schließen
$mysqli->close();
?>
