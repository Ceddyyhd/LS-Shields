<?php
include 'db.php';  // Datenbankverbindung einbinden

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Werte aus dem POST holen
    $vacation_id = $_POST['vacation_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $note = $_POST['note'];  // Die Notiz aus dem Formular holen
    $user_name = $_SESSION['username'];  // Benutzername aus der Session holen

    try {
        // SQL-Abfrage zum Aktualisieren des Urlaubsantrags mit der Notiz
        $sql_update = "UPDATE vacations SET start_date = ?, end_date = ?, status = ?, note = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([$start_date, $end_date, $status, $note, $vacation_id]);

        // Log-Eintrag erstellen
        $action = "Urlaubsantrag (ID: $vacation_id) bearbeitet. Startdatum: $start_date, Enddatum: $end_date, Status: $status, Notiz: $note";
        $sql_log = "INSERT INTO vacations_logs (vacation_id, action, user_name) VALUES (?, ?, ?)";
        $stmt_log = $conn->prepare($sql_log);
        $stmt_log->execute([$vacation_id, $action, $user_name]);

        echo json_encode(['success' => true, 'message' => 'Urlaubsantrag erfolgreich bearbeitet.']);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
