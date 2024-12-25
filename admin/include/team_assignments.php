<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Datenbankverbindung einbinden
include('db.php');

// Überprüfen, ob die richtigen Daten empfangen wurden
if (isset($_POST['team_data']) && isset($_POST['event_id'])) {
    $teamData = $_POST['team_data']; // Array der Teamdaten
    $eventId = $_POST['event_id']; // Event ID

    // Debugging: Gib die empfangenen Daten aus
    error_log("Team Data: " . print_r($teamData, true)); // Gibt die Team-Daten aus

    // Transaktion starten (um alle Änderungen in einem Schritt zu machen)
    $conn->beginTransaction();

    try {
        // Durch jedes Team in den empfangenen Daten iterieren
        foreach ($teamData as $team) {
            // Debugging: Gib das Team aus
            error_log("Team: " . print_r($team, true)); // Gibt jedes Team aus

            $teamName = $team['team_name']; // Teamname
            $areaName = $team['bereich']; // Bereichname
            $teamId = isset($team['team_id']) ? $team['team_id'] : null; // Wenn Team ID vorhanden ist, holen wir sie

            // Prüfen, ob das Team bereits existiert (basierend auf event_id und team_name)
            if ($teamId) {
                // Team existiert bereits, also updaten wir es
                $stmt = $conn->prepare("UPDATE teams SET team_name = :team_name, area_name = :area_name WHERE id = :team_id");
                $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
                $stmt->bindValue(':area_name', $areaName, PDO::PARAM_STR);
                $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                // Überprüfen, ob das Team mit der gleichen event_id und team_name bereits existiert
                $stmt = $conn->prepare("SELECT id FROM teams WHERE event_id = :event_id AND team_name = :team_name LIMIT 1");
                $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
                $stmt->execute();
                $existingTeam = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingTeam) {
                    // Team existiert, hole die ID des bestehenden Teams
                    $teamId = $existingTeam['id'];
                } else {
                    // Wenn das Team nicht existiert, fügen wir es hinzu
                    $stmt = $conn->prepare("INSERT INTO teams (event_id, team_name, area_name) VALUES (:event_id, :team_name, :area_name)");
                    $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                    $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
                    $stmt->bindValue(':area_name', $areaName, PDO::PARAM_STR);
                    $stmt->execute();

                    // Hole die ID des neu erstellten Teams
                    $teamId = $conn->lastInsertId();  // ID des neu erstellten Teams holen
                }
            }

            // Gehe durch alle Mitarbeiter und aktualisiere sie oder füge sie hinzu
            foreach ($team['employee_names'] as $index => $employee) {
                // Sicherstellen, dass employee ein Array ist
                if (!is_array($employee)) {
                    error_log("Fehler: $employee ist kein Array!");
                }
            
                $employeeName = $employee['name']; // Mitarbeitername
                $employeeId = isset($employee['id']) ? $employee['id'] : null; // Mitarbeiter ID, wenn vorhanden
            
                // Prüfen, ob der Mitarbeiter bereits existiert
                if ($employeeId) {
                    // Mitarbeiter existiert, also updaten wir ihn
                    $stmt = $conn->prepare("UPDATE employees SET employee_name = :employee_name, is_team_lead = :is_team_lead WHERE id = :employee_id");
                    $stmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                    $stmt->bindValue(':is_team_lead', $employee['is_team_lead'] == '1' ? 1 : 0, PDO::PARAM_INT); // Der Mitarbeiter ist Team Lead, wenn `is_team_lead` 1 ist
                    $stmt->bindValue(':employee_id', $employeeId, PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    // Mitarbeiter existiert nicht, also fügen wir ihn hinzu
                    $stmt = $conn->prepare("INSERT INTO employees (team_id, employee_name, is_team_lead) VALUES (:team_id, :employee_name, :is_team_lead)");
                    $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                    $stmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                    $stmt->bindValue(':is_team_lead', $employee['is_team_lead'] == '1' ? 1 : 0, PDO::PARAM_INT); // Der Mitarbeiter ist Team Lead, wenn `is_team_lead` 1 ist
                    $stmt->execute();
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
