<?php
// Fehleranzeige aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: ../error.php');
    exit;
}

// Überprüfen, ob die Daten gesendet wurden
if (isset($_POST['event_id']) && isset($_POST['employees']) && isset($_POST['InputNotiz'])) {
    $eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
    $employeeIds = $_POST['employees'];
    $notiz = filter_input(INPUT_POST, 'InputNotiz', FILTER_SANITIZE_STRING);  // Notizen holen

    // Überprüfen, ob $employeeIds ein Array ist
    if (!is_array($employeeIds)) {
        // Falls es kein Array ist, in ein Array umwandeln
        $employeeIds = explode(',', $employeeIds);
    }

    try {
        // Verbindung zur Datenbank
        include('db.php');

        // Alle ausgewählten Mitarbeiter für das Event in die event_mitarbeiter_anmeldung-Tabelle eintragen
        $stmt = $conn->prepare("INSERT INTO event_mitarbeiter_anmeldung (event_id, employee_id, notizen) VALUES (:event_id, :employee_id, :notizen)");

        foreach ($employeeIds as $employeeId) {
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
            $stmt->bindParam(':notizen', $notiz, PDO::PARAM_STR);  // Ändern auf PDO::PARAM_STR
            $stmt->execute();
        }

        // Loggen des Eintrags
        logAction('INSERT', 'event_mitarbeiter_anmeldung', 'event_id: ' . $eventId . ', employee_ids: ' . implode(',', $employeeIds));

        echo json_encode(['success' => true, 'message' => 'Anmeldung erfolgreich!']);
    } catch (PDOException $e) {
        error_log('Fehler beim Speichern der Anmeldung: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern der Anmeldung: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Fehlende Daten!']);
    exit;
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
