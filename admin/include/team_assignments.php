<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Datenbankverbindung einbinden
include('db.php');

// Überprüfen, ob die richtigen Daten empfangen wurden
if (isset($_POST['team_data']) && isset($_POST['event_id'])) {
    $teamData = $_POST['team_data']; // Array der Teamdaten
    $eventId = $_POST['event_id']; // Event ID

    // Transaktion starten (um alle Änderungen in einem Schritt zu machen)
    $conn->beginTransaction();

    try {
        $insertEmployees = [];
        $updateEmployees = [];

        // Durch jedes Team in den empfangenen Daten iterieren
        foreach ($teamData as $team) {
            $teamName = $team['team_name']; // Teamname
            $areaName = $team['bereich']; // Bereichname

            // Überprüfen, ob das Team bereits existiert
            $stmt = $conn->prepare("SELECT id FROM teams WHERE event_id = :event_id AND team_name = :team_name LIMIT 1");
            $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
            $stmt->execute();
            $existingTeam = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingTeam) {
                // Team existiert, wir holen die Team-ID
                $teamId = $existingTeam['id'];
            } else {
                // Team existiert nicht, also fügen wir es hinzu
                $stmt = $conn->prepare("INSERT INTO teams (event_id, team_name, area_name) VALUES (:event_id, :team_name, :area_name)");
                $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
                $stmt->bindValue(':area_name', $areaName, PDO::PARAM_STR);
                $stmt->execute();
                $teamId = $conn->lastInsertId();  // ID des neu erstellten Teams holen
            }

            // Gehe durch alle Mitarbeiter und aktualisiere oder füge sie hinzu
            foreach ($team['employee_names'] as $index => $employeeName) {
                // Überprüfen, ob der Mitarbeiter bereits existiert
                $stmt = $conn->prepare("SELECT id FROM employees WHERE team_id = :team_id AND employee_name = :employee_name LIMIT 1");
                $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                $stmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                $stmt->execute();
                $existingEmployee = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingEmployee) {
                    // Mitarbeiter existiert, Update durchführen
                    $stmt = $conn->prepare("UPDATE employees SET employee_name = :employee_name, is_team_lead = :is_team_lead WHERE id = :id");
                    $stmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                    $stmt->bindValue(':is_team_lead', $index == 0 ? 1 : 0, PDO::PARAM_INT); // Der erste Mitarbeiter ist der Team Lead
                    $stmt->bindValue(':id', $existingEmployee['id'], PDO::PARAM_INT);
                    $stmt->execute(); // Mitarbeiter aktualisieren
                } else {
                    // Mitarbeiter existiert nicht, neuen Mitarbeiter hinzufügen
                    $stmt = $conn->prepare("INSERT INTO employees (team_id, employee_name, is_team_lead) VALUES (:team_id, :employee_name, :is_team_lead)");
                    $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                    $stmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                    $stmt->bindValue(':is_team_lead', $index == 0 ? 1 : 0, PDO::PARAM_INT); // Der erste Mitarbeiter ist der Team Lead
                    $stmt->execute(); // Mitarbeiter hinzufügen
                }
            }
        }

        // Commit der Transaktion: Alle Änderungen werden in einem Schritt gespeichert
        $conn->commit();

        // Erfolgsantwort zurückgeben
        echo json_encode(['status' => 'success', 'message' => 'Teams erfolgreich gespeichert']);
    } catch (Exception $e) {
        // Fehler bei der Speicherung, Rollback der Transaktion
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Fehler: ' . $e->getMessage()]);
    }
} else {
    // Fehlerantwort, wenn keine Daten empfangen wurden
    echo json_encode(['status' => 'error', 'message' => 'Fehlende Daten']);
}
?>
