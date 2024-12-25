<?php
include('db.php');

// Eingabedaten aus POST
$eventId = $_POST['event_id'];  // Event-ID (wird mit dem Event verknüpft)
$bereich = $_POST['bereich'];  // Bereich (z. B. Haupteingang, Nebeneingang)
$mitarbeiter = $_POST['mitarbeiter'];  // Array von Mitarbeitern
$teamName = $_POST['team_name'];  // Der Name des Teams

// Die Mitarbeiter in die Datenbank einfügen
foreach ($mitarbeiter as $index => $employee) {
    // Falls der Mitarbeiter der Team Lead ist
    $isTeamLead = ($index == 0) ? true : false;  // Angenommen, der erste Mitarbeiter ist der Team Lead
    
    // SQL-Abfrage zum Einfügen der Daten
    $query = "INSERT INTO team_assignments (event_id, team_name, area_name, employee_name, is_team_lead)
              VALUES (:event_id, :team_name, :area_name, :employee_name, :is_team_lead)";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':event_id', $eventId);
    $stmt->bindParam(':team_name', $teamName);
    $stmt->bindParam(':area_name', $bereich);
    $stmt->bindParam(':employee_name', $employee);
    $stmt->bindParam(':is_team_lead', $isTeamLead, PDO::PARAM_BOOL);
    $stmt->execute();
}

echo json_encode(['status' => 'success', 'message' => 'Team erfolgreich erstellt!']);
?>
