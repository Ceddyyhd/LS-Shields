<?php
include 'db.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$created_by = $_POST['created_by'] ?? null;  // Der Benutzer, der den Rabatt gelöscht hat

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

    echo json_encode(['success' => true, 'message' => 'Rabatt erfolgreich gelöscht']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Löschen des Rabatts: ' . $e->getMessage()]);
}
?>
