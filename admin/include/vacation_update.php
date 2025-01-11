<?php
include 'db.php';  // Datenbankverbindung einbinden

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Werte aus dem POST holen
    $vacation_id = $_POST['vacation_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $note = $_POST['note'];  // Die Notiz aus dem Formular holen
    $user_name = $_POST['user_name'];  // Benutzername aus dem hidden Input holen

    try {
        // SQL-Abfrage zum Abrufen der alten Daten
        $sql_select = "SELECT * FROM vacations WHERE id = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->execute([$vacation_id]);
        $old_vacation = $stmt_select->fetch(PDO::FETCH_ASSOC);

        // Änderungen ermitteln
        $changes = [];
        if ($start_date !== $old_vacation['start_date']) {
            $changes[] = "Startdatum: " . $old_vacation['start_date'] . " -> " . $start_date;
        }
        if ($end_date !== $old_vacation['end_date']) {
            $changes[] = "Enddatum: " . $old_vacation['end_date'] . " -> " . $end_date;
        }
        if ($status !== $old_vacation['status']) {
            $changes[] = "Status: " . ucfirst($old_vacation['status']) . " -> " . ucfirst($status);
        }
        if ($note !== $old_vacation['note']) {
            $changes[] = "Notiz: " . $old_vacation['note'] . " -> " . $note;
        }

        // Nur loggen, wenn es Änderungen gab
        if (count($changes) > 0) {
            $action = "Urlaubsantrag (ID: $vacation_id) bearbeitet: " . implode(", ", $changes);

            // Log-Eintrag erstellen
            $sql_log = "INSERT INTO vacations_logs (vacation_id, action, user_name) VALUES (?, ?, ?)";
            $stmt_log = $conn->prepare($sql_log);
            $stmt_log->execute([$vacation_id, $action, $user_name]);
        }

        // Urlaubsantrag in der DB aktualisieren
        $sql_update = "UPDATE vacations SET start_date = ?, end_date = ?, status = ?, note = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([$start_date, $end_date, $status, $note, $vacation_id]);

        echo json_encode(['success' => true, 'message' => 'Urlaubsantrag erfolgreich bearbeitet.']);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
