<?php
include 'db.php';  // Datenbankverbindung einbinden

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Werte aus dem POST holen
    $vacation_id = $_POST['vacation_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $note = $_POST['note'];  // Die Notiz aus dem Formular holen

    try {
        // SQL-Abfrage zum Aktualisieren des Urlaubsantrags mit der Notiz
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
