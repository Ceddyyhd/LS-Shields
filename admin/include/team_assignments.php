<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Datenbankverbindung einbinden
include('db.php');

// Überprüfung der empfangenen Teamdaten
if (isset($_POST['teams']) && !empty($_POST['teams'])) {
    // Empfangen der Teamdaten aus dem AJAX-Request
    $teamData = $_POST['teams'];

    // Beginne die Transaktion (für alle Teams gleichzeitig)
    $conn->beginTransaction();

    try {
        // Durch alle Teams iterieren
        foreach ($teamData as $team) {
            $teamName = $team['team_name'];
            $teamArea = $team['area_name'];

            // Team in der Datenbank speichern
            $stmt = $conn->prepare("INSERT INTO teams (team_name, area_name) VALUES (:team_name, :area_name)");
            $stmt->bindValue(':team_name', $teamName, PDO::PARAM_STR);
            $stmt->bindValue(':area_name', $teamArea, PDO::PARAM_STR);
            $stmt->execute();

            // Team-ID des neuen Teams abrufen
            $teamId = $conn->lastInsertId();

            // Mitarbeiterdaten speichern
            foreach ($team['employee_names'] as $employee) {
                $employeeName = $employee['name'];
                $isTeamLead = $employee['is_team_lead'];

                // Mitarbeiter in der Datenbank speichern
                $stmt = $conn->prepare("INSERT INTO employees (team_id, employee_name, is_team_lead) VALUES (:team_id, :employee_name, :is_team_lead)");
                $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                $stmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                $stmt->bindValue(':is_team_lead', $isTeamLead, PDO::PARAM_INT);
                $stmt->execute();
            }
        }

        // Bestätigen der Transaktion
        $conn->commit();
        echo "Erfolgreich gespeichert.";
    } catch (Exception $e) {
        // Bei einem Fehler die Transaktion zurücksetzen
        $conn->rollBack();
        echo "Fehler: " . $e->getMessage();
    }
}
?>
