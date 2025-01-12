<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

// Fehleranzeige für Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Einbinden der Datenbankverbindung
include('db.php');

// Überprüfen, ob das Formular abgesendet wurde
if (isset($_POST['summernoteContent']) && isset($_POST['id'])) {
    $summernoteContent = $_POST['summernoteContent'];
    $id = $_POST['id'];

    try {
        // SQL-Abfrage zum Aktualisieren der `leitfaden`-Spalte in der `ausbildungstypen`-Tabelle
        $stmt = $conn->prepare("UPDATE ausbildungstypen SET leitfaden = :summernoteContent WHERE id = :id");
        $stmt->bindParam(':summernoteContent', $summernoteContent, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Die Abfrage ausführen
        $stmt->execute();

        // Erfolgreiche Speicherung
        echo json_encode(['success' => true, 'message' => 'Daten wurden erfolgreich gespeichert!']);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern der Daten: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Fehlende Daten!']);
}
?>
