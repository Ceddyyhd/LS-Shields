<?php
include 'db.php';  // Datenbankverbindung einbinden

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Werte aus dem Formular holen
    $user_id = $_POST['user_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $note = $_POST['note'];
    $created_by = $_SESSION['username'];  // Angemeldeter Benutzer

    try {
        // Urlaub in der Datenbank speichern
        $sql_insert = "INSERT INTO vacations (user_id, start_date, end_date, status, erstellt_von, note)
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->execute([$user_id, $start_date, $end_date, $status, $created_by, $note]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
