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
    // Du kannst hier einen Wert aus den Teamdaten oder etwas anderes setzen
    $vornameNachname = "Unbekannt";  // Setze einen Standardwert oder wähle einen dynamischen Wert aus

    try {
        // Beginne die Transaktion
        $conn->beginTransaction();

        // Bereite das SQL-Statement vor, um die Daten in die Tabelle `eventplanung` einzufügen
        $stmt = $conn->prepare("INSERT INTO eventplanung (team_verteilung, vorname_nachname) VALUES (:team_verteilung, :vorname_nachname)");

        // Binde die JSON-Daten und den Namen ein
        $stmt->bindValue(':team_verteilung', $teamDataJson, PDO::PARAM_STR);
        $stmt->bindValue(':vorname_nachname', $vornameNachname, PDO::PARAM_STR);

        // Führe das SQL-Statement aus
        if ($stmt->execute()) {
            // Holen der zuletzt eingefügten ID
            $lastId = $conn->lastInsertId();
            error_log("Daten erfolgreich eingefügt. Letzte eingefügte ID: " . $lastId);
            
            // Bestätigen der Transaktion
            $conn->commit();
            echo "Erfolgreich gespeichert.";
        } else {
            // Fehler beim Ausführen des Insert-Statements
            error_log("Fehler beim Einfügen der Daten.");
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
