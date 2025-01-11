<?php
include('db.php');

// Überprüfen, ob die benötigten Parameter übergeben wurden
if (isset($_POST['event_id']) && isset($_POST['employee_id'])) {
    $eventId = $_POST['event_id'];
    $employeeId = $_POST['employee_id'];

    try {
        // SQL-Abfrage, um den Eintrag zu löschen
        $stmt = $conn->prepare("DELETE FROM event_mitarbeiter_anmeldung WHERE event_id = :event_id AND employee_id = :employee_id");
        $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
        $stmt->execute();

        // Rückgabe einer erfolgreichen Antwort
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Fehlende Parameter']);
}
?>
