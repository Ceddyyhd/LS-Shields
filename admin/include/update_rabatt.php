<?php
include 'db.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$display_name = $_POST['display_name'] ?? null;
$description = $_POST['description'] ?? null;
$rabatt_percent = $_POST['rabatt_percent'] ?? null;
$updated_by = $_SESSION['username'] ?? 'Unbekannt';  // Benutzername aus der Session holen

if (!$id || !$display_name || !$description || !$rabatt_percent) {
    echo json_encode(['success' => false, 'error' => 'Fehlende Eingabewerte']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE rabatt SET display_name = :display_name, description = :description, rabatt_percent = :rabatt_percent WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':display_name' => $display_name,
        ':description' => $description,
        ':rabatt_percent' => $rabatt_percent
    ]);

    // Log-Eintrag für das Bearbeiten
    $logStmt = $conn->prepare("INSERT INTO rabatt_logs (rabatt_id, action, changed_by) VALUES (:rabatt_id, :action, :changed_by)");
    $logStmt->execute([
        ':rabatt_id' => $id,
        ':action' => 'Bearbeitet',
        ':changed_by' => $updated_by  // Der Benutzer, der die Änderung vorgenommen hat
    ]);

    echo json_encode(['success' => true, 'message' => 'Ankündigung erfolgreich bearbeitet']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Bearbeiten der Ankündigung: ' . $e->getMessage()]);
}
?>
