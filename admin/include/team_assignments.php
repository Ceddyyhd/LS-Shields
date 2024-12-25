<?php
include('db.php');

// Eingabedaten aus POST
$eventId = $_POST['event_id']; // Event ID
$bereich = $_POST['bereich']; // Bereich
$mitarbeiter = $_POST['mitarbeiter']; // Mitarbeiter-Array
$teamName = $_POST['team_name']; // Team Name

// SQL-Abfrage zum Hinzufügen des Teams
$query = "INSERT INTO team_assignments (event_id, team_name, area_name, employee_name, is_team_lead)
          VALUES (:event_id, :team_name, :area_name, :employee_name, :is_team_lead)";
$stmt = $conn->prepare($query);

foreach ($mitarbeiter as $index => $employee) {
    if (trim($employee) !== '') {  // Nur nicht-leere Mitarbeiter einfügen
        $isTeamLead = ($index == 0) ? true : false;  // Angenommen, der erste Mitarbeiter ist der Team Lead

        $stmt->bindParam(':event_id', $eventId);
        $stmt->bindParam(':team_name', $teamName);
        $stmt->bindParam(':area_name', $bereich);
        $stmt->bindParam(':employee_name', $employee);
        $stmt->bindParam(':is_team_lead', $isTeamLead, PDO::PARAM_BOOL);
        $stmt->execute();
    }
}

echo json_encode(['status' => 'success', 'message' => 'Team erfolgreich erstellt!']);
?>
