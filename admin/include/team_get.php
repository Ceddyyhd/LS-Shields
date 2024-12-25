<?php
include('db.php');

// Überprüfen, ob eine Event-ID übergeben wurde
if (isset($_GET['event_id'])) {
    $eventId = $_GET['event_id'];

    try {
        // Abfrage, um die Teams und alle Mitarbeiter für jedes Team zu holen
        $query = "SELECT t.id AS team_id, t.team_name, t.area_name, e.id AS employee_id, e.employee_name, e.is_team_lead
                  FROM teams t
                  LEFT JOIN employees e ON e.team_id = t.id
                  WHERE t.event_id = :event_id
                  ORDER BY t.team_name";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':event_id', $eventId);
        $stmt->execute();

        // Ergebnisse abrufen
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Team-Daten gruppieren
        $groupedTeams = [];
        foreach ($teams as $team) {
            // Wenn das Team noch nicht existiert, füge es hinzu
            if (!isset($groupedTeams[$team['team_name']])) {
                $groupedTeams[$team['team_name']] = [
                    'team_name' => $team['team_name'],
                    'area_name' => $team['area_name'],
                    'employee_names' => [],
                ];
            }

            // Füge den Mitarbeiter zum entsprechenden Team hinzu
            $groupedTeams[$team['team_name']]['employee_names'][] = [
                'name' => $team['employee_name'],
                'is_team_lead' => $team['is_team_lead'],
                'id' => $team['employee_id'], // Mitarbeiter ID
            ];
        }

        // Gebe die gruppierten Teams als JSON zurück
        echo json_encode(array_values($groupedTeams));
    } catch (PDOException $e) {
        // Fehler bei der Datenbankabfrage
        echo json_encode(['status' => 'error', 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Keine Event-ID angegeben']);
}
?>
