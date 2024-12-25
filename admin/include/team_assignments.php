<?php
include('db.php');

// Eingabedaten aus POST
$teamData = $_POST['team_data']; // Array der Teamdaten
$eventId = $_POST['event_id']; // Event ID

// Die Teamdaten in die Datenbank einfügen
foreach ($teamData as $team) {
    $isTeamLead = false; // Standardwert für Team Lead

    // Wir gehen davon aus, dass das Array der Mitarbeiter mehr als ein Element enthält
    $teamMembers = explode(",", $team['employee_names']); // Angenommen, die Mitarbeiter sind als kommagetrennte Liste übermittelt

    // Für den ersten Mitarbeiter im Team setzen wir is_team_lead auf true
    foreach ($teamMembers as $index => $employeeName) {
        $isTeamLead = ($index == 0); // Der erste Mitarbeiter ist der Team Lead
        
        // SQL-Abfrage zum Einfügen der Team- und Mitarbeiterdaten
        $query = "INSERT INTO team_assignments (event_id, team_name, area_name, employee_name, is_team_lead)
                  VALUES (:event_id, :team_name, :area_name, :employee_name, :is_team_lead)";
        
        // Vorbereiten der SQL-Abfrage
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':event_id', $eventId);
        $stmt->bindParam(':team_name', $team['team_name']);
        $stmt->bindParam(':area_name', $team['bereich']);
        $stmt->bindParam(':employee_name', $employeeName);
        $stmt->bindParam(':is_team_lead', $isTeamLead, PDO::PARAM_BOOL);

        // Führe die SQL-Abfrage aus, um das Team und den Mitarbeiter zu speichern
        $stmt->execute();
    }
}

echo json_encode(['status' => 'success', 'message' => 'Teams erfolgreich erstellt!']);
?>
