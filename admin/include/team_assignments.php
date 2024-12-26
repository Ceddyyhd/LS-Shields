<?php
include('db.php');

// Fehlerbehandlung aktivieren
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Überprüfen, ob die Teamdaten gesendet wurden
if (isset($_POST['teams']) && !empty($_POST['teams'])) {
    $teamData = $_POST['teams'];

    // Debugging: Prüfe, was wir erhalten
    error_log("Empfangene Team-Daten: " . print_r($teamData, true));

    // Die Teamdaten in JSON umwandeln
    $teamDataJson = json_encode($teamData);
    
    // Überprüfe, ob JSON korrekt codiert wurde
    if ($teamDataJson === false) {
        error_log("JSON-Fehler: " . json_last_error_msg());
        echo "Fehler bei der JSON-Codierung.";
        exit;
    }

    // Debugging: Überprüfen des generierten JSON
    error_log("Team-Daten (JSON): " . $teamDataJson);

    try {
        // Beginne die Transaktion
        $conn->beginTransaction();

        // Bereite das SQL-Statement vor, um die Daten in die Tabelle `eventplanung` einzufügen
        $stmt = $conn->prepare("INSERT INTO eventplanung (team_verteilung) VALUES (:team_verteilung)");

        // Binde die JSON-Daten in das `team_verteilung`-Feld ein
        $stmt->bindValue(':team_verteilung', $teamDataJson, PDO::PARAM_STR);

        // Führe das SQL-Statement aus und prüfe, ob es erfolgreich war
        $result = $stmt->execute();
        
        if ($result) {
            // Erfolgreiches Einfügen
            error_log("Daten erfolgreich eingefügt.");
            $conn->commit();
            echo "Erfolgreich gespeichert.";
        } else {
            // Fehler bei der Ausführung des Statements
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
