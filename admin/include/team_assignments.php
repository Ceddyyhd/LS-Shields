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
        // Gehe durch jedes Team
        foreach ($teamData as $team) {
            $teamName = $team['team_name'];
            $areaName = $team['bereich'];

            // Prüfen, ob das Team bereits existiert
            $stmt = $conn->prepare("SELECT id FROM team_assignments WHERE event_id = :event_id AND team_name = :team_name LIMIT 1");
            $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
            $stmt->execute();

            $existingTeam = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingTeam) {
                // Team existiert, also Mitarbeiter aktualisieren
                $teamId = $existingTeam['id'];

                // Gehe durch alle Mitarbeiter und aktualisiere sie
                foreach ($team['employee_names'] as $index => $employeeName) {
                    // Überprüfen, ob der Mitarbeiter bereits im Team existiert
                    $stmt = $conn->prepare("SELECT id FROM team_assignments WHERE event_id = :event_id AND team_name = :team_name AND employee_name = :employee_name LIMIT 1");
                    $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                    $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
                    $stmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                    $stmt->execute();

                    $existingEmployee = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($existingEmployee) {
                        // Wenn der Mitarbeiter existiert, aktualisiere den Datensatz
                        $stmt = $conn->prepare("UPDATE team_assignments SET employee_name = :employee_name, is_team_lead = :is_team_lead WHERE id = :id");
                        $stmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                        $stmt->bindValue(':is_team_lead', $index == 0 ? 1 : 0, PDO::PARAM_INT); // Der erste Mitarbeiter ist der Team Lead
                        $stmt->bindValue(':id', $existingEmployee['id'], PDO::PARAM_INT);
                        $stmt->execute();
                    } else {
                        // Wenn der Mitarbeiter nicht existiert, füge ihn hinzu
                        $stmt = $conn->prepare("INSERT INTO team_assignments (event_id, team_name, area_name, employee_name, is_team_lead) VALUES (:event_id, :team_name, :area_name, :employee_name, :is_team_lead)");
                        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                        $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
                        $stmt->bindValue(':area_name', $areaName, PDO::PARAM_STR);
                        $stmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                        $stmt->bindValue(':is_team_lead', $index == 0 ? 1 : 0, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }
            } else {
                // Wenn das Team nicht existiert, erstelle es
                $stmt = $conn->prepare("INSERT INTO team_assignments (event_id, team_name, area_name) VALUES (:event_id, :team_name, :area_name)");
                $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
                $stmt->bindValue(':area_name', $areaName, PDO::PARAM_STR);
                $stmt->execute();

                // Hole die ID des neuen Teams
                $teamId = $conn->lastInsertId();

                // Füge alle Mitarbeiter für das neue Team hinzu
                foreach ($team['employee_names'] as $index => $employeeName) {
                    $stmt = $conn->prepare("INSERT INTO team_assignments (event_id, team_name, area_name, employee_name, is_team_lead) VALUES (:event_id, :team_name, :area_name, :employee_name, :is_team_lead)");
                    $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                    $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
                    $stmt->bindValue(':area_name', $areaName, PDO::PARAM_STR);
                    $stmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                    $stmt->bindValue(':is_team_lead', $index == 0 ? 1 : 0, PDO::PARAM_INT); // Der erste Mitarbeiter ist der Team Lead
                    $stmt->execute();
                }
            }
        }

        // Commit der Transaktion
        $conn->commit();

        echo json_encode(['status' => 'success', 'message' => 'Teams erfolgreich gespeichert']);
    } catch (Exception $e) {
        // Fehler bei der Speicherung, Rollback der Transaktion
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Fehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Fehlende Daten']);
}
?>
