<?php
include('db.php');

// Fehlerbehandlung aktivieren
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Überprüfen, ob die Teamdaten gesendet wurden
if (isset($_POST['teams']) && !empty($_POST['teams'])) {
    $teamData = $_POST['teams'];

    // Die Teamdaten in JSON umwandeln
    $teamDataJson = json_encode($teamData);

    // Überprüfe, ob JSON korrekt codiert wurde
    if ($teamDataJson === false) {
        error_log("JSON-Fehler: " . json_last_error_msg());
        echo "Fehler bei der JSON-Codierung.";
        exit;
    }

    // Beispiel für die Generierung des Werts für vorname_nachname
    $vornameNachname = "Unbekannt";  // Setze einen Standardwert oder wähle einen dynamischen Wert aus

    try {
        // Beginne die Transaktion
        $conn->beginTransaction();

        // Überprüfen, ob ein Event mit dieser ID bereits existiert
        $eventId = $_GET['id']; // Annahme, dass die ID aus der URL kommt

        // Zuerst prüfen, ob das Event bereits existiert
        $stmt = $conn->prepare("SELECT * FROM eventplanung WHERE id = :id");
        $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $existingEvent = $stmt->fetch(PDO::FETCH_ASSOC);

        // Wenn das Event existiert, ein UPDATE durchführen, andernfalls ein INSERT
        if ($existingEvent) {
            // UPDATE-Befehl für das bestehende Event
            $stmt = $conn->prepare("UPDATE eventplanung SET team_verteilung = :team_verteilung, vorname_nachname = :vorname_nachname WHERE id = :id");
            $stmt->bindValue(':team_verteilung', $teamDataJson, PDO::PARAM_STR);
            $stmt->bindValue(':vorname_nachname', $vornameNachname, PDO::PARAM_STR);
            $stmt->bindValue(':id', $eventId, PDO::PARAM_INT);
            $stmt->execute();

            error_log("Event mit ID $eventId wurde erfolgreich aktualisiert.");
        } else {
            // Wenn das Event nicht existiert, ein INSERT durchführen
            $stmt = $conn->prepare("INSERT INTO eventplanung (team_verteilung, vorname_nachname) VALUES (:team_verteilung, :vorname_nachname)");
            $stmt->bindValue(':team_verteilung', $teamDataJson, PDO::PARAM_STR);
            $stmt->bindValue(':vorname_nachname', $vornameNachname, PDO::PARAM_STR);
            $stmt->execute();

            error_log("Neues Event wurde erfolgreich eingefügt.");
        }

        // Mitarbeiterdaten ebenfalls aktualisieren oder hinzufügen
        foreach ($teamData as $team) {
            foreach ($team['employee_names'] as $employee) {
                $employeeName = $employee['name'];
                $isTeamLead = $employee['is_team_lead'];

                // Hier können wir auch ein INSERT oder UPDATE für die Mitarbeiter durchführen
                // Wenn Mitarbeiter bereits existiert, UPDATE durchführen, ansonsten INSERT
                $employeeStmt = $conn->prepare("SELECT * FROM employees WHERE employee_name = :employee_name AND team_id = :team_id");
                $employeeStmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                $employeeStmt->bindValue(':team_id', $eventId, PDO::PARAM_INT);
                $employeeStmt->execute();

                $existingEmployee = $employeeStmt->fetch(PDO::FETCH_ASSOC);

                if ($existingEmployee) {
                    // UPDATE für existierenden Mitarbeiter
                    $employeeUpdateStmt = $conn->prepare("UPDATE employees SET is_team_lead = :is_team_lead WHERE employee_id = :employee_id");
                    $employeeUpdateStmt->bindValue(':is_team_lead', $isTeamLead, PDO::PARAM_INT);
                    $employeeUpdateStmt->bindValue(':employee_id', $existingEmployee['employee_id'], PDO::PARAM_INT);
                    $employeeUpdateStmt->execute();
                } else {
                    // INSERT für neuen Mitarbeiter
                    $employeeInsertStmt = $conn->prepare("INSERT INTO employees (team_id, employee_name, is_team_lead) VALUES (:team_id, :employee_name, :is_team_lead)");
                    $employeeInsertStmt->bindValue(':team_id', $eventId, PDO::PARAM_INT);
                    $employeeInsertStmt->bindValue(':employee_name', $employeeName, PDO::PARAM_STR);
                    $employeeInsertStmt->bindValue(':is_team_lead', $isTeamLead, PDO::PARAM_INT);
                    $employeeInsertStmt->execute();
                }
            }
        }

        // Bestätigen der Transaktion
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
