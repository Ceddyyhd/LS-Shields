<?php
// Fehleranzeige aktivieren
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Datenbankverbindung einbinden
include('db.php');

// Überprüfen, ob die Teamdaten gesendet wurden
if (isset($_POST['teams']) && !empty($_POST['teams'])) {
    // Empfange die Teamdaten
    $teamData = $_POST['teams'];

    // Protokolliere die empfangenen Teamdaten (zur Überprüfung)
    error_log("Empfangene Team-Daten: " . print_r($teamData, true));  // Diese Zeile gibt die empfangenen Daten im Log aus

    // Die Teamdaten in JSON umwandeln
    $teamDataJson = json_encode($teamData);

    // Überprüfen, ob JSON korrekt codiert wurde
    if ($teamDataJson === false) {
        error_log("Fehler bei der JSON-Codierung: " . json_last_error_msg());  // Fehler bei der JSON-Codierung
        echo "Fehler bei der JSON-Codierung.";
        exit;
    }

    try {
        // Beginne die Transaktion
        $conn->beginTransaction();

        // Die ID aus der URL holen (Event ID)
        if (isset($_GET['id'])) {
            $eventId = $_GET['id'];  // Beispiel Event ID aus der URL
        } else {
            die('Keine Eventplanungs-ID angegeben.');
        }

        // UPDATE-Statement für das bestehende Event mit der entsprechenden ID
        $stmt = $conn->prepare("UPDATE eventplanung SET team_verteilung = :team_verteilung WHERE id = :id");
        $stmt->bindParam(':team_verteilung', $teamDataJson, PDO::PARAM_STR);
        $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);

        // Führe das UPDATE-Statement aus
        if ($stmt->execute()) {
            // Bestätigen der Transaktion
            $conn->commit();
            echo "Daten wurden erfolgreich gespeichert!";
        } else {
            error_log("Fehler beim Ausführen des UPDATE-Statements: " . implode(", ", $stmt->errorInfo()));
            echo "Fehler beim Speichern der Daten.";
        }

    } catch (PDOException $e) {
        // Fehlerbehandlung: Transaktion zurücksetzen
        $conn->rollBack();
        error_log("Fehler: " . $e->getMessage());
        echo "Fehler: " . $e->getMessage();
    }
} else {
    echo "Fehlende Team-Daten!";
}
?>
