<?php
// Datenbankverbindung einbinden
include 'db.php';
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

// Überprüfen, ob die location per POST gesendet wurde
if (isset($_POST['location'])) {
    $location = $_POST['location'];

    try {
        // Wenn location "Unbekannt" ist, also NULL in der Datenbank
        if ($location === 'Unbekannt') {
            // Löschen der Einträge, bei denen die location NULL ist
            $stmt = $conn->prepare("DELETE FROM deckel WHERE location = ''");
        } else {
            // Löschen der Einträge mit der angegebenen Location
            $stmt = $conn->prepare("DELETE FROM deckel WHERE location = :location");
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        }
        $stmt->execute();

        // Log-Eintrag für das Löschen
        logAction('DELETE', 'deckel', 'location: ' . $location . ', deleted_by: ' . $_SESSION['user_id']);

        // Erfolgsantwort zurückgeben
        echo json_encode(['success' => true, 'message' => 'Erfolgreich gelöscht']);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        error_log('Fehler beim Löschen der Location: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
} else {
    // Fehler, wenn keine Location übermittelt wurde
    echo json_encode(['success' => false, 'message' => 'Keine Location angegeben']);
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
