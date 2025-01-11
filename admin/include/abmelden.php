<?php
session_start();

// Überprüfen, ob die Anfrage von einem AJAX-Request kommt
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    die(json_encode(['success' => false, 'error' => 'Unauthorized access']));
}

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die(json_encode(['success' => false, 'error' => 'Invalid CSRF token']));
}

include('db.php');

// Überprüfen, ob die benötigten Parameter übergeben wurden
if (isset($_POST['event_id']) && isset($_POST['employee_id'])) {
    $eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
    $employeeId = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

    if ($eventId === false || $employeeId === false) {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
        exit;
    }

    try {
        // SQL-Abfrage, um den Eintrag zu löschen
        $stmt = $conn->prepare("DELETE FROM event_mitarbeiter_anmeldung WHERE event_id = :event_id AND employee_id = :employee_id");
        $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
        $stmt->execute();

        // Loggen des Löschvorgangs
        logAction('DELETE', 'event_mitarbeiter_anmeldung', 'event_id: ' . $eventId . ', employee_id: ' . $employeeId);

        // Rückgabe einer erfolgreichen Antwort
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        error_log('Database error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
}

// Funktion zum Loggen von Aktionen
function logAction($action, $table, $details) {
    global $conn;

    // SQL-Abfrage zum Einfügen des Log-Eintrags
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, details, user_id, timestamp) VALUES (:action, :table_name, :details, :user_id, NOW())");
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':table_name', $table, PDO::PARAM_STR);
    $stmt->bindParam(':details', $details, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
}
?>
