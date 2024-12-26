<?php
include('db.php');

// Überprüfen, ob die Teamdaten gesendet wurden
if (isset($_POST['teams']) && !empty($_POST['teams'])) {
    $teamData = $_POST['teams'];

    try {
        // Beginne die Transaktion
        $conn->beginTransaction();

        // Debugging: Ausgabe der empfangenen Team-Daten
        error_log("Team-Daten zum Speichern: " . print_r($teamData, true));

        foreach ($teamData as $team) {
            // Team in die Datenbank einfügen
            $stmt = $conn->prepare("INSERT INTO teams (team_name, area_name) VALUES (:team_name, :area_name)");
            $stmt->bindValue(':team_name', $team['team_name'], PDO::PARAM_STR);
            $stmt->bindValue(':area_name', $team['area_name'], PDO::PARAM_STR);
            $stmt->execute();

            // Team-ID des neu eingefügten Teams holen
            $teamId = $conn->lastInsertId();

            // Debugging: Ausgabe der Team-ID und Mitarbeiter
            error_log("Team gespeichert. Team-ID: " . $teamId);

            // Mitarbeiter hinzufügen (nur mit gültigem Namen)
            foreach ($team['employee_names'] as $employee) {
                if (!empty($employee['name'])) { // Nur Mitarbeiter mit Namen hinzufügen
                    $stmt = $conn->prepare("INSERT INTO employees (team_id, employee_name, is_team_lead) VALUES (:team_id, :employee_name, :is_team_lead)");
                    $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                    $stmt->bindValue(':employee_name', $employee['name'], PDO::PARAM_STR);
                    $stmt->bindValue(':is_team_lead', $employee['is_team_lead'], PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        }

        // Transaktion bestätigen
        $conn->commit();
        echo "Erfolgreich gespeichert.";
    } catch (Exception $e) {
        // Fehlerbehandlung: Transaktion zurücksetzen
        $conn->rollBack();
        error_log("Fehler: " . $e->getMessage());
        echo "Fehler: " . $e->getMessage();
    }
} else {
    echo "Keine Team-Daten empfangen.";
}
?>
