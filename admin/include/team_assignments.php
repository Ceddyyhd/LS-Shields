<?php
include('db.php');

// Eingabedaten aus POST
$eventId = $_POST['event_id']; // Event ID
$bereich = $_POST['bereich']; // Bereich (z. B. Haupteingang)
$mitarbeiter = $_POST['mitarbeiter']; // Array von Mitarbeitern
$teamName = $_POST['team_name']; // Team Name

// Team bearbeiten
$query = "UPDATE team_assignments 
          SET team_name = :team_name, area_name = :area_name, employee_name = :employee_name, is_team_lead = :is_team_lead
          WHERE event_id = :event_id";
$stmt = $conn->prepare($query);

foreach ($mitarbeiter as $index => $employee) {
    // Falls der Mitarbeiter der Team Lead ist
    $isTeamLead = ($index == 0) ? true : false;  // Angenommen, der erste Mitarbeiter ist der Team Lead

    // SQL-Abfrage zum Bearbeiten der Daten
    $stmt->bindParam(':event_id', $eventId);
    $stmt->bindParam(':team_name', $teamName);
    $stmt->bindParam(':area_name', $bereich);
    $stmt->bindParam(':employee_name', $employee);
    $stmt->bindParam(':is_team_lead', $isTeamLead, PDO::PARAM_BOOL);
    $stmt->execute();
}

echo json_encode(['status' => 'success', 'message' => 'Team erfolgreich bearbeitet!']);
?>
