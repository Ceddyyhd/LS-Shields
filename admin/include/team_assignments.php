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
        $newTeams = [];

        // Durch jedes Team in den empfangenen Daten iterieren
        foreach ($teamData as $team) {
            $teamName = $team['team_name']; // Teamname
            $areaName = $team['bereich']; // Bereichname
            $teamId = isset($team['team_id']) ? $team['team_id'] : null; // Team ID, falls vorhanden

            // Wenn das Team nicht existiert, füge es hinzu
            if (!$teamId) {
                $stmt = $conn->prepare("INSERT INTO team_assignments (event_id, team_name, area_name) VALUES (:event_id, :team_name, :area_name)");
                $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
                $stmt->bindValue(':area_name', $areaName, PDO::PARAM_STR);
                $stmt->execute();
                // Die ID des neu erstellten Teams
                $teamId = $conn->lastInsertId();
                $newTeams[] = $teamId; // Speichern der neuen Team-ID
            }

            // Gehe durch alle Mitarbeiter und aktualisiere oder füge sie hinzu
            foreach ($team['employee_names'] as $index => $employeeName) {
                $employeeId = isset($team['employee_ids'][$index]) ? $team['employee_ids'][$index] : null; // Mitarbeiter-ID

                if ($employeeId) {
                    // Wenn der Mitarbeiter existiert, füge ihn zum Update-Array hinzu
                    $updateEmployees[] = [
                        'id' => $employeeId,
                        'employee_name' => $employeeName,
                        'is_team_lead' => $index == 0 ? 1 : 0,
                    ];
                } else {
                    // Wenn der Mitarbeiter nicht existiert, füge ihn zum Insert-Array hinzu
                    $insertEmployees[] = [
                        'event_id' => $eventId,
                        'team_name' => $teamName,
                        'area_name' => $areaName,
                        'employee_name' => $employeeName,
                        'is_team_lead' => $index == 0 ? 1 : 0,
                        'team_id' => $teamId, // Das Team ID für den neuen Mitarbeiter
                    ];
                }
            }
        }

        // 1. UPDATE-Mitarbeiter in einem einzigen Schritt
        if (count($updateEmployees) > 0) {
            $updateQuery = "UPDATE team_assignments SET employee_name = CASE id ";
            foreach ($updateEmployees as $employee) {
                $updateQuery .= "WHEN {$employee['id']} THEN '{$employee['employee_name']}' ";
            }
            $updateQuery .= "END, is_team_lead = CASE id ";
            foreach ($updateEmployees as $employee) {
                $updateQuery .= "WHEN {$employee['id']} THEN {$employee['is_team_lead']} ";
            }
            $updateQuery .= "END WHERE id IN (" . implode(',', array_column($updateEmployees, 'id')) . ")";
            $conn->exec($updateQuery);
        }

        // 2. INSERT-Mitarbeiter in einem einzigen Schritt
        if (count($insertEmployees) > 0) {
            $insertQuery = "INSERT INTO team_assignments (event_id, team_name, area_name, employee_name, is_team_lead, team_id) VALUES ";
            $values = [];
            foreach ($insertEmployees as $employee) {
                $values[] = "({$employee['event_id']}, '{$employee['team_name']}', '{$employee['area_name']}', '{$employee['employee_name']}', {$employee['is_team_lead']}, {$employee['team_id']})";
            }
            $insertQuery .= implode(", ", $values);
            $conn->exec($insertQuery);
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
