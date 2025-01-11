<?php
include 'db.php';
session_start();
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$display_name = $_POST['display_name'] ?? null;
$description = $_POST['description'] ?? null;
$rabatt_percent = $_POST['rabatt_percent'] ?? null;
$created_by = $_POST['created_by'] ?? null;  // Benutzername aus dem hidden input holen

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

if (!$id || !$display_name || !$description || !$rabatt_percent || !$created_by) {
    echo json_encode(['success' => false, 'error' => 'Fehlende Eingabewerte']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE rabatt SET display_name = :display_name, description = :description, rabatt_percent = :rabatt_percent, created_by = :created_by WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':display_name' => $display_name,
        ':description' => $description,
        ':rabatt_percent' => $rabatt_percent,
        ':created_by' => $created_by  // Der Benutzer, der die Änderung vorgenommen hat
    ]);

    // Log-Eintrag für das Bearbeiten
    $logStmt = $conn->prepare("INSERT INTO rabatt_logs (rabatt_id, action, changed_by) VALUES (:rabatt_id, :action, :changed_by)");
    $logStmt->execute([
        ':rabatt_id' => $id,
        ':action' => 'Bearbeitet',
        ':changed_by' => $created_by  // Der Benutzer, der die Änderung vorgenommen hat
    ]);

    // Allgemeiner Log-Eintrag
    logAction('UPDATE', 'rabatt', 'id: ' . $id . ', bearbeitet von: ' . $_SESSION['user_id']);

    echo json_encode(['success' => true, 'message' => 'Rabatt erfolgreich bearbeitet']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Bearbeiten des Rabatts: ' . $e->getMessage()]);
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
