<?php
include('db.php');

// Eingabedaten aus POST
$teamData = $_POST['team_data']; // Array der Teamdaten
$eventId = $_POST['event_id']; // Event ID

// Die Teamdaten in die Datenbank einfügen
foreach ($teamData as $team) {
    // SQL-Abfrage zum Einfügen der Daten
    $query = "INSERT INTO team_assignments (event_id, team_name, area_name, employee_name, is_team_lead)
              VALUES (:event_id, :team_name, :area_name, :employee_name, :is_team_lead)";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':event_id', $eventId);
    $stmt->bindParam(':team_name', $team['team_name']);
    $stmt->bindParam(':area_name', $team['bereich']);
    $stmt->bindParam(':employee_name', $team['employee_name']);
    $stmt->bindParam(':is_team_lead', $team['is_team_lead'], PDO::PARAM_BOOL);
    $stmt->execute();
}

echo json_encode(['status' => 'success', 'message' => 'Teams erfolgreich erstellt!']);
?>
