<?php
include('db.php');

// Überprüfen, ob die Teamdaten gesendet wurden
if (isset($_POST['teams']) && !empty($_POST['teams'])) {
    $teamData = $_POST['teams'];

    // Debugging: Prüfe, was wir erhalten
    error_log("Empfangene Team-Daten: " . print_r($teamData, true));
    
    // Die Teamdaten in JSON umwandeln
    $teamDataJson = json_encode($teamData);
    error_log("Team-Daten (JSON): " . $teamDataJson);  // Überprüfe, ob die JSON-Codierung korrekt funktioniert

    try {
        // Beginne die Transaktion
        $conn->beginTransaction();

        // Bereite das SQL-Statement vor, um die Daten in die Tabelle `eventplanung` einzufügen
        $stmt = $conn->prepare("INSERT INTO eventplanung (team_verteilung) VALUES (:team_verteilung)");

        // Binde die JSON-Daten in das `team_verteilung`-Feld ein
        $stmt->bindValue(':team_verteilung', $teamDataJson, PDO::PARAM_STR);

        // Führe das SQL-Statement aus
        $stmt->execute();

        // Transaktion bestätigen
        $conn->commit();
        echo "Erfolgreich gespeichert.";
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
