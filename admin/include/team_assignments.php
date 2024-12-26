<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Datenbankverbindung einbinden
include('db.php');

// Überprüfung der empfangenen Teamdaten
if (isset($_POST['teams']) && !empty($_POST['teams'])) {
    // Empfangen der Teamdaten aus dem AJAX-Request
    $teamData = $_POST['teams'];

    // Die Event-ID aus der URL holen
    $eventId = isset($_GET['id']) ? $_GET['id'] : null;

    if ($eventId) {
        // Beginne die Transaktion (für alle Teams gleichzeitig)
        $conn->beginTransaction();

        try {
            // Die Teamdaten in JSON umwandeln
            $teamDataJson = json_encode($teamData);

            // Eventplanung in der Datenbank speichern (inklusive der Teamdaten als JSON)
            $stmt = $conn->prepare("UPDATE eventplanung SET team_verteilung = :team_verteilung WHERE id = :id");
            $stmt->bindValue(':team_verteilung', $teamDataJson, PDO::PARAM_STR);
            $stmt->bindValue(':id', $eventId, PDO::PARAM_INT);
            $stmt->execute();

            // Bestätigen der Transaktion
            $conn->commit();
            echo "Erfolgreich gespeichert.";
        } catch (Exception $e) {
            // Bei einem Fehler die Transaktion zurücksetzen
            $conn->rollBack();
            echo "Fehler: " . $e->getMessage();
        }
    } else {
        echo "Event-ID nicht gefunden.";
    }
}
?>
