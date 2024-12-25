<?php
include('db.php');

// Überprüfen, ob die richtigen Daten empfangen wurden
if (isset($_POST['team_data'])) {
    $teamData = $_POST['team_data']; // Array der Teamdaten
    $eventId = $_POST['event_id']; // Event ID

    // Ausgabe der empfangenen Daten zur Überprüfung
    var_dump($teamData);
    exit; // Damit wir die empfangenen Daten sehen können

    // Die Teamdaten in die Datenbank einfügen
    foreach ($teamData as $team) {
        $isTeamLead = false; // Standardwert für Team Lead

        // Gehe durch alle Mitarbeiter des Teams
        foreach ($team['employee_names'] as $index => $employeeName) {
            $isTeamLead = ($index == 0); // Der erste Mitarbeiter ist der Team Lead

            // SQL-Abfrage zum Einfügen der Team- und Mitarbeiterdaten
            $query = "INSERT INTO team_assignments (event_id, team_name, area_name, employee_name, is_team_lead)
                      VALUES (:event_id, :team_name, :area_name, :employee_name, :is_team_lead)";

            // Vorbereiten der SQL-Abfrage
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                // Fehler bei der Vorbereitung der SQL-Abfrage
                echo "Fehler bei der Vorbereitung der SQL-Abfrage: " . $conn->errorInfo();
                exit;
            }

            // Binden der Parameter
            $stmt->bindParam(':event_id', $eventId);
            $stmt->bindParam(':team_name', $team['team_name']);
            $stmt->bindParam(':area_name', $team['bereich']);
            $stmt->bindParam(':employee_name', $employeeName);
            $stmt->bindParam(':is_team_lead', $isTeamLead, PDO::PARAM_BOOL);

            // Führe die SQL-Abfrage aus, um das Team und den Mitarbeiter zu speichern
            if (!$stmt->execute()) {
                // Fehler bei der Ausführung der SQL-Abfrage
                $errorInfo = $stmt->errorInfo();
                echo "SQL Fehler: " . $errorInfo[2];
                exit;
            }
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Teams erfolgreich erstellt!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Keine Team-Daten empfangen']);
}
?>
