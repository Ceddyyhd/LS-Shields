<?php
// Fehleranzeige für Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Einbinden der Datenbankverbindung
include('db.php'); // PDO-Verbindung einbinden

// Überprüfen, ob der Summernote-Inhalt gesendet wurde
if (isset($_POST['summernoteContent'])) {
    // Den Inhalt von Summernote abholen
    $summernoteContent = $_POST['summernoteContent'];

    try {
        // SQL-Abfrage zum Speichern des Inhalts mit PDO
        $stmt = $conn->prepare("INSERT INTO eventplanung (summernote_content) VALUES (:summernoteContent)");
        $stmt->bindParam(':summernoteContent', $summernoteContent, PDO::PARAM_STR);

        // Die Abfrage ausführen
        $stmt->execute();
        
        echo "Daten wurden erfolgreich gespeichert!";
    } catch (PDOException $e) {
        echo "Fehler beim Speichern der Daten: " . $e->getMessage();
    }
} else {
    echo "Fehlende Daten!";
}

?>
