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
        // Beginne die Transaktion
        $conn->beginTransaction();

        try {
            // Die Teamdaten in JSON umwandeln
            $teamDataJson = json_encode($teamData);
            
            if (!$teamDataJson) {
                throw new Exception("JSON-Encoding ist fehlgeschlagen: " . json_last_error_msg());
            }

            // Überprüfen, ob ein Event mit dieser ID bereits existiert
            $stmt = $conn->prepare("SELECT id FROM eventplanung WHERE id = :id");
            $stmt->bindValue(':id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($event) {
                // Event existiert, wir aktualisieren die `team_verteilung` Spalte
                $stmt = $conn->prepare("UPDATE eventplanung SET team_verteilung = :team_verteilung WHERE id = :id");
                $stmt->bindValue(':team_verteilung', $teamDataJson, PDO::PARAM_STR);
                $stmt->bindValue(':id', $eventId, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                // Event existiert nicht, wir erstellen einen neuen Eintrag
                $stmt = $conn->prepare("INSERT INTO eventplanung (id, team_verteilung) VALUES (:id, :team_verteilung)");
                $stmt->bindValue(':id', $eventId, PDO::PARAM_INT);
                $stmt->bindValue(':team_verteilung', $teamDataJson, PDO::PARAM_STR);
                $stmt->execute();
            }

            // Bestätigen der Transaktion
            $conn->commit();
            echo "Erfolgreich gespeichert.";
        } catch (Exception $e) {
            // Bei einem Fehler die Transaktion zurücksetzen und Fehler ausgeben
            $conn->rollBack();
            echo "Fehler: " . $e->getMessage();
        }
    } else {
        echo "Event-ID nicht gefunden.";
    }
}
?>
