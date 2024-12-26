<?php
// Fehleranzeige für Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Einbinden der Datenbankverbindung
include('db.php');

// Überprüfen, ob die Teamdaten gesendet wurden
if (isset($_POST['teams']) && !empty($_POST['teams'])) {
    $teamData = $_POST['teams'];

    // Fehlerprotokollierung: Ausgabe der empfangenen Team-Daten
    error_log("Empfangene Team-Daten: " . print_r($teamData, true)); // Diese Zeile gibt die empfangenen Daten in den Fehler-Log aus

    // Die Teamdaten in JSON umwandeln
    $teamDataJson = json_encode($teamData);

    // Überprüfen, ob JSON korrekt codiert wurde
    if ($teamDataJson === false) {
        echo "Fehler bei der JSON-Codierung.";
        exit;
    }

    try {
        // Beginne die Transaktion
        $conn->beginTransaction();

        // Die ID aus der URL holen (wir gehen davon aus, dass diese immer gesetzt wird)
        if (isset($_GET['id'])) {
            $eventId = $_GET['id'];  // Die Event-ID aus der URL (z.B. eventplanung_akte.php?id=1)
        } else {
            die('Keine Eventplanungs-ID angegeben.');
        }

        // UPDATE-Befehl für das bestehende Event mit der entsprechenden ID
        $stmt = $conn->prepare("UPDATE eventplanung SET team_verteilung = :team_verteilung WHERE id = :id");
        $stmt->bindParam(':team_verteilung', $teamDataJson, PDO::PARAM_STR);
        $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);

        // Führe das UPDATE-Statement aus
        if ($stmt->execute()) {
            // Bestätigen der Transaktion
            $conn->commit();
            echo "Daten wurden erfolgreich gespeichert!";
        } else {
            echo "Fehler beim Speichern der Daten.";
        }

    } catch (PDOException $e) {
        echo "Fehler beim Speichern der Daten: " . $e->getMessage();
    }
} else {
    echo "Fehlende Daten!";
}
?>
