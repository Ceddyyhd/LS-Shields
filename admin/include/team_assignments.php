<?php
include('db.php');

// Fehlerbehandlung aktivieren
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Überprüfen, ob die Teamdaten gesendet wurden
if (isset($_POST['teams']) && !empty($_POST['teams'])) {
    $teamData = $_POST['teams'];

    // Die Teamdaten in JSON umwandeln
    $teamDataJson = json_encode($teamData);

    // Überprüfe, ob JSON korrekt codiert wurde
    if ($teamDataJson === false) {
        error_log("JSON-Fehler: " . json_last_error_msg());
        echo "Fehler bei der JSON-Codierung.";
        exit;
    }

    // Beispiel für die Generierung des Werts für vorname_nachname
    $vornameNachname = "Unbekannt";  // Setze einen Standardwert oder wähle einen dynamischen Wert aus

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
        $stmt = $conn->prepare("UPDATE eventplanung SET team_verteilung = :team_verteilung, vorname_nachname = :vorname_nachname WHERE id = :id");
        $stmt->bindValue(':team_verteilung', $teamDataJson, PDO::PARAM_STR);
        $stmt->bindValue(':vorname_nachname', $vornameNachname, PDO::PARAM_STR);
        $stmt->bindValue(':id', $eventId, PDO::PARAM_INT);

        // Führe das UPDATE-Statement aus
        if ($stmt->execute()) {
            // Bestätigen der Transaktion
            $conn->commit();
            echo "Erfolgreich gespeichert.";
        } else {
            // Fehler beim Ausführen des UPDATE-Statements
            error_log("Fehler beim Aktualisieren der Daten.");
            $conn->rollBack();
            echo "Fehler beim Speichern der Daten.";
        }

    } catch (Exception $e) {
        // Fehlerbehandlung: Transaktion zurücksetzen
        $conn->rollBack();
        error_log("Fehler: " . $e->getMessage());
        echo "Fehler: " . $e->getMessage();
    }
} else {
    echo "Keine Team-Daten empfangen.";
}
?>
