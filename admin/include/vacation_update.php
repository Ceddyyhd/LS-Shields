<?php
include 'db.php'; // Datenbankverbindung einbinden

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vacation_id = $_POST['vacation_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $note = $_POST['note'];

    try {
        // SQL-Abfrage zum Aktualisieren des Urlaubsantrags
        $sql = "UPDATE vacations SET start_date = ?, end_date = ?, status = ?, note = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$start_date, $end_date, $status, $note, $vacation_id]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}
?>
