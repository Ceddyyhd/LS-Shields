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
$created_by = $_SESSION['user_id'] ?? null;  // Der Benutzer, der den Rabatt gelöscht hat

if (!$id || !$created_by) {
    echo json_encode(['success' => false, 'error' => 'Fehlende ID oder Benutzer']);
    exit;
}

try {
    // Log-Eintrag für das Löschen
    $logStmt = $conn->prepare("INSERT INTO rabatt_logs (rabatt_id, action, changed_by) VALUES (:rabatt_id, :action, :changed_by)");
    $logStmt->execute([
        ':rabatt_id' => $id,
        ':action' => 'Gelöscht',
        ':changed_by' => $created_by  // Der Benutzer, der den Rabatt gelöscht hat
    ]);

    // Rabatt löschen
    $stmt = $conn->prepare("DELETE FROM rabatt WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // Log-Eintrag für das Löschen
    logAction('DELETE', 'rabatt', 'rabatt_id: ' . $id . ', deleted_by: ' . $created_by);

    echo json_encode(['success' => true, 'message' => 'Rabatt erfolgreich gelöscht']);
} catch (PDOException $e) {
    error_log('Fehler beim Löschen des Rabatts: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Fehler beim Löschen des Rabatts: ' . $e->getMessage()]);
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
