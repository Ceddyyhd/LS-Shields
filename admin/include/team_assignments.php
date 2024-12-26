<?php
include('db.php');

// Überprüfen, ob die Teamdaten gesendet wurden
if (isset($_POST['teams']) && !empty($_POST['teams'])) {
    $teamData = $_POST['teams'];

    try {
        // Beginne die Transaktion
        $conn->beginTransaction();

        foreach ($teamData as $team) {
            // Team in die Datenbank einfügen
            $stmt = $conn->prepare("INSERT INTO teams (team_name, area_name) VALUES (:team_name, :area_name)");
            $stmt->bindValue(':team_name', $team['team_name'], PDO::PARAM_STR);
            $stmt->bindValue(':area_name', $team['area_name'], PDO::PARAM_STR);
            $stmt->execute();

            $teamId = $conn->lastInsertId();  // ID des neu eingefügten Teams

            // Mitarbeiter hinzufügen
            foreach ($team['employee_names'] as $employee) {
                $stmt = $conn->prepare("INSERT INTO employees (team_id, employee_name, is_team_lead) VALUES (:team_id, :employee_name, :is_team_lead)");
                $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                $stmt->bindValue(':employee_name', $employee['name'], PDO::PARAM_STR);
                $stmt->bindValue(':is_team_lead', $employee['is_team_lead'], PDO::PARAM_INT);
                $stmt->execute();
            }
        }

        // Bestätigen der Transaktion
        $conn->commit();
        echo "Erfolgreich gespeichert.";
    } catch (Exception $e) {
        // Bei Fehlern die Transaktion zurücksetzen
        $conn->rollBack();
        echo "Fehler: " . $e->getMessage();
    }
} else {
    echo "Keine Team-Daten empfangen.";
}
?>
