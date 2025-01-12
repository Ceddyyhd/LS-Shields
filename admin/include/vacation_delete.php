<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include 'db.php';  // Datenbankverbindung einbinden

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vacation_id'])) {
    $vacation_id = $_POST['vacation_id'];

    try {
        // Urlaubsantrag aus der Datenbank löschen
        $sql_delete = "DELETE FROM vacations WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->execute([$vacation_id]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
