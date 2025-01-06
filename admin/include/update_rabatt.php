<?php
include 'db.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$key_name = $_POST['key_name'] ?? null;
$description = $_POST['description'] ?? null;
$rabatt_percent = $_POST['rabatt_percent'] ?? null;
$created_by = $_POST['created_by'] ?? null;  // Benutzername aus dem Formular holen

if (!$id || !$key_name || !$description || !$rabatt_percent || !$created_by) {
    echo json_encode(['success' => false, 'error' => 'Fehlende Eingabewerte']);
    exit;
}

try {
    // Rabatt aktualisieren
    $stmt = $conn->prepare("UPDATE rabatt SET key_name = :key_name, description = :description, rabatt_percent = :rabatt_percent WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':key_name' => $key_name,
        ':description' => $description,
        ':rabatt_percent' => $rabatt_percent
    ]);

    // Log-Eintrag für das Bearbeiten
    $logStmt = $conn->prepare("INSERT INTO rabatt_logs (rabatt_id, action, changed_by) VALUES (:rabatt_id, :action, :changed_by)");
    $logStmt->execute([
        ':rabatt_id' => $id,
        ':action' => 'Bearbeitet',
        ':changed_by' => $created_by  // Der Benutzer, der den Rabatt bearbeitet hat
    ]);

    echo json_encode(['success' => true, 'message' => 'Rabatt erfolgreich bearbeitet']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Bearbeiten des Rabatts: ' . $e->getMessage()]);
}
?>
