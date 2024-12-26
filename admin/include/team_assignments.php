<?php
// Beispiel für die manuelle Speicherung in die Datenbank

include('db.php');

// Manuell erstellter JSON-String für Team-Daten
$teamDataJson = json_encode([
    [
        'team_name' => 'Team 1',
        'area_name' => 'Bereich 1',
        'employee_names' => [
            ['name' => 'Mitarbeiter Lead', 'is_team_lead' => 1],
            ['name' => 'Test', 'is_team_lead' => 0]
        ]
    ]
]);

// Überprüfen, ob JSON korrekt codiert wurde
if ($teamDataJson === false) {
    error_log("Fehler bei der JSON-Codierung: " . json_last_error_msg());
    echo "Fehler bei der JSON-Codierung.";
    exit;
}

try {
    // Event ID, die du testen möchtest (z.B. 1)
    $eventId = 1;

    // SQL-UPDATE-Statement, um die Team-Daten in die Datenbank zu speichern
    $stmt = $conn->prepare("UPDATE eventplanung SET team_verteilung = :team_verteilung WHERE id = :id");
    $stmt->bindParam(':team_verteilung', $teamDataJson, PDO::PARAM_STR);
    $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);

    // Führe das UPDATE-Statement aus
    if ($stmt->execute()) {
        echo "Daten wurden erfolgreich gespeichert!";
    } else {
        error_log("Fehler beim Ausführen des UPDATE-Statements: " . implode(", ", $stmt->errorInfo()));
        echo "Fehler beim Speichern der Daten.";
    }
} catch (PDOException $e) {
    error_log("Fehler beim Speichern der Daten: " . $e->getMessage());
    echo "Fehler: " . $e->getMessage();
}
?>
