<?php
include 'db.php';
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Fehlende ID']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM ankuendigung WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // Log-Eintrag für das Löschen
    logAction('DELETE', 'ankuendigung', 'ankuendigung_id: ' . $id . ', deleted_by: ' . $_SESSION['user_id']);

    echo json_encode(['success' => true, 'message' => 'Ankündigung erfolgreich gelöscht']);
} catch (PDOException $e) {
    error_log('Fehler beim Löschen der Ankündigung: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Fehler beim Löschen der Ankündigung: ' . $e->getMessage()]);
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
