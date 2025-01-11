<?php
include 'db.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$display_name = $_POST['display_name'] ?? null;
$description = $_POST['description'] ?? null;
$rabatt_percent = $_POST['rabatt_percent'] ?? null;
$created_by = $_POST['created_by'] ?? null;  // Benutzername aus dem hidden input holen

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

    echo json_encode(['success' => true, 'message' => 'Rabatt erfolgreich bearbeitet']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Bearbeiten des Rabatts: ' . $e->getMessage()]);
}
?>
