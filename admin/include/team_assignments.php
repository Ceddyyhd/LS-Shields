<?php
// Fehleranzeige für Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Einbinden der Datenbankverbindung
include('db.php');

// Überprüfen, ob die Team-Daten abgesendet wurden
if (isset($_POST['teams']) && isset($_POST['id'])) {
    $teamData = $_POST['teams'];
    $eventId = $_POST['id'];

    // Die Teamdaten in JSON umwandeln
    $teamDataJson = json_encode($teamData);

    // Überprüfen, ob JSON korrekt codiert wurde
    if ($teamDataJson === false) {
        echo "Fehler bei der JSON-Codierung.";
        exit;
    }

    try {
        // SQL-Abfrage zum Aktualisieren der Teamverteilung
        $stmt = $conn->prepare("UPDATE eventplanung SET team_verteilung = :team_verteilung WHERE id = :id");
        $stmt->bindParam(':team_verteilung', $teamDataJson, PDO::PARAM_STR);
        $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);

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
