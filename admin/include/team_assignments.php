<?php
// Fehleranzeige für Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Einbinden der Datenbankverbindung
include('db.php');

// Manuell die Teamdaten einfügen
$teamData = [
    [
        'team_name' => 'de',
        'area_name' => 'de',
        'employee_names' => [
            ['name' => 'de', 'is_team_lead' => '1'],
            ['name' => 'de', 'is_team_lead' => '0'],
            ['name' => 'de', 'is_team_lead' => '0'],
        ]
    ]
];

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

    // Setze hier die Event-ID (z. B. aus der URL)
    $eventId = 1; // Beispiel Event-ID, bitte anpassen

    // Binde die Parameter und führe die Abfrage aus
    $stmt->bindParam(':team_verteilung', $teamDataJson, PDO::PARAM_STR);
    $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);

    // Führe das UPDATE-Statement aus
    if ($stmt->execute()) {
        echo "Daten wurden erfolgreich gespeichert!";
    } else {
        echo "Fehler beim Speichern der Daten.";
    }
} catch (PDOException $e) {
    echo "Fehler beim Speichern der Daten: " . $e->getMessage();
}
?>
