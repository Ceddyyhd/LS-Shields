<?php
// Fehleranzeige für Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Einbinden der Datenbankverbindung
include('db.php');
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

// Überprüfen, ob das Formular abgesendet wurde
if (isset($_POST['summernoteContent']) && isset($_POST['id'])) {
    $summernoteContent = $_POST['summernoteContent'];
    $id = $_POST['id'];

    try {
        // SQL-Abfrage zum Aktualisieren der Eventplanung
        $stmt = $conn->prepare("UPDATE eventplanung SET summernote_content = :summernoteContent WHERE id = :id");
        $stmt->bindParam(':summernoteContent', $summernoteContent, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Die Abfrage ausführen
        $stmt->execute();

        // Log-Eintrag für die Änderungen
        logAction('UPDATE', 'eventplanung', 'id: ' . $id . ', summernote_content aktualisiert von: ' . $_SESSION['user_id']);

        echo json_encode(['success' => true, 'message' => 'Daten wurden erfolgreich gespeichert!']);
    } catch (PDOException $e) {
        error_log('Fehler beim Speichern der Daten: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern der Daten: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Fehlende Daten!']);
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
