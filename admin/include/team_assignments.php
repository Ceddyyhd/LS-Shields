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
        // Durch jedes Team in den empfangenen Daten iterieren
        foreach ($teamData as $team) {
            $teamName = $team['team_name']; // Teamname
            $areaName = $team['bereich']; // Bereichname
            $teamId = isset($team['team_id']) ? $team['team_id'] : null; // Team ID, falls vorhanden

            // Prüfen, ob das Team bereits existiert (basierend auf event_id und team_name)
            if ($teamId) {
                // Wenn Team ID vorhanden, dann UPDATE, andernfalls INSERT
                $stmt = $conn->prepare("SELECT id FROM team_assignments WHERE event_id = :event_id AND id = :team_id LIMIT 1");
                $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                $stmt->execute();
                $existingTeam = $stmt->fetch(PDO::FETCH_ASSOC); // Wenn das Team existiert, wird es hier geladen
            }

            if ($existingTeam) {
                // Wenn das Team existiert, dann Mitarbeiter aktualisieren
                foreach ($team['employee_names'] as $index => $employeeName) {
                    $employeeId = isset($team['employee_ids'][$index]) ? $team['employee_ids'][$index] : null; // Mitarbeiter-ID

                    if ($employeeId) {
                        // Wenn der Mitarbeiter existiert, aktualisiere den Datensatz
                        $stmt = $conn->prepare("UPDATE team_assignments SET employee_name = :employee_name, is_team_lead = :is_team_lead WHERE id = :id");
                        $stmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                        $stmt->bindValue(':is_team_lead', $index == 0 ? 1 : 0, PDO::PARAM_INT); // Der erste Mitarbeiter ist der Team Lead
                        $stmt->bindValue(':id', $employeeId, PDO::PARAM_INT);
                        $stmt->execute(); // Mitarbeiter aktualisieren
                    } else {
                        // Wenn der Mitarbeiter nicht existiert, füge ihn hinzu
                        $stmt = $conn->prepare("INSERT INTO team_assignments (event_id, team_name, area_name, employee_name, is_team_lead) VALUES (:event_id, :team_name, :area_name, :employee_name, :is_team_lead)");
                        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                        $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
                        $stmt->bindValue(':area_name', $areaName, PDO::PARAM_STR);
                        $stmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                        $stmt->bindValue(':is_team_lead', $index == 0 ? 1 : 0, PDO::PARAM_INT); // Der erste Mitarbeiter ist der Team Lead
                        $stmt->execute(); // Mitarbeiter hinzufügen
                    }
                }
            } else {
                // Wenn das Team nicht existiert, füge es hinzu
                $stmt = $conn->prepare("INSERT INTO team_assignments (event_id, team_name, area_name) VALUES (:event_id, :team_name, :area_name)");
                $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
                $stmt->bindValue(':area_name', $areaName, PDO::PARAM_STR);
                $stmt->execute(); // Team in die DB einfügen

                // Hole die ID des neu erstellten Teams
                $teamId = $conn->lastInsertId();

                // Füge alle Mitarbeiter für das neue Team hinzu
                foreach ($team['employee_names'] as $index => $employeeName) {
                    $stmt = $conn->prepare("INSERT INTO team_assignments (event_id, team_name, area_name, employee_name, is_team_lead) VALUES (:event_id, :team_name, :area_name, :employee_name, :is_team_lead)");
                    $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                    $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
                    $stmt->bindValue(':area_name', $areaName, PDO::PARAM_STR);
                    $stmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                    $stmt->bindValue(':is_team_lead', $index == 0 ? 1 : 0, PDO::PARAM_INT); // Der erste Mitarbeiter ist der Team Lead
                    $stmt->execute(); // Mitarbeiter für das neue Team hinzufügen
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
