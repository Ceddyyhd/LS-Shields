<?php
require_once 'db.php'; // Deine DB-Verbindungsdatei
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

// Überprüfen, ob eine ID übergeben wurde
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        // Daten in die Archiv-Tabelle verschieben
        $sql = "INSERT INTO ausbildungstypen_alt (id, key_name, display_name, description) SELECT id, key_name, display_name, description FROM ausbildungstypen WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ausführen der Einfüge-Abfrage
        $stmt->execute();

        // Datensatz aus der ursprünglichen Tabelle löschen
        $sqlDelete = "DELETE FROM ausbildungstypen WHERE id = :id";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
        
        // Löschen ausführen
        $stmtDelete->execute();

        // Log-Eintrag für das Löschen
        logAction('DELETE', 'ausbildungstypen', 'ausbildungstyp_id: ' . $id . ', deleted_by: ' . $_SESSION['user_id']);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        error_log('Fehler beim Archivieren des Ausbildungstyps: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Archivieren des Ausbildungstyps: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine ID angegeben.']);
}

$conn = null; // Verbindung schließen

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
