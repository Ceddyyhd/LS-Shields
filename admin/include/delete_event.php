<?php
require 'db.php';  // Deine DB-Verbindung
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

// Sicherstellen, dass eine Event-ID übergeben wurde
if (isset($_POST['event_id'])) {
    $event_id = (int) $_POST['event_id'];

    try {
        // SQL-Abfrage, um den Status des Events auf 'Gelöscht' zu setzen
        $sql = "UPDATE eventplanung SET status = 'Gelöscht' WHERE id = :event_id";

        // Vorbereiten und Ausführen der SQL-Abfrage
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Log-Eintrag für das Löschen
            logAction('DELETE', 'eventplanung', 'event_id: ' . $event_id . ', deleted_by: ' . $_SESSION['user_id']);

            // Erfolgreiche Antwort zurückgeben
            echo json_encode(['success' => true, 'message' => 'Event erfolgreich gelöscht']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Löschen des Events']);
        }
    } catch (PDOException $e) {
        error_log('Fehler beim Löschen des Events: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Löschen des Events: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine Event-ID übergeben']);
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
