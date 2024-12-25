<?php
include('db.php');

// Fehleranzeige aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Eingabedaten aus POST
if (isset($_POST['team_data'])) {
    $teamData = $_POST['team_data']; // Array der Teamdaten
    $eventId = $_POST['event_id']; // Event ID

    // Debug-Ausgabe der empfangenen Daten
    var_dump($teamData);
    exit;

    // Die Teamdaten in die Datenbank einfügen
    foreach ($teamData as $team) {
        if (!isset($team['team_name'], $team['bereich'], $team['employee_names'])) {
            // Fehlerbehandlung, falls die erwarteten Felder fehlen
            echo "Fehlende Felder: team_name, bereich oder employee_names";
            exit;
        }

        $isTeamLead = false; // Standardwert für Team Lead
        $employeeNames = $team['employee_names']; // Mitarbeiter-Liste

        // Für jeden Mitarbeiter des Teams
        foreach ($employeeNames as $index => $employeeName) {
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
                echo "Fehler bei der Ausführung der SQL-Abfrage: " . implode(", ", $stmt->errorInfo());
                exit;
            }
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Teams erfolgreich erstellt!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Keine Team-Daten empfangen']);
}
?>
