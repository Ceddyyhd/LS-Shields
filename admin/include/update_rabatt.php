<?php
include 'db.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$key_name = $_POST['key_name'] ?? null;
$description = $_POST['description'] ?? null;
$rabatt_percent = $_POST['rabatt_percent'] ?? null;
$updated_by = $_SESSION['username'] ?? 'Unbekannt';

if (!$id || !$key_name || !$description || !$rabatt_percent) {
    echo json_encode(['success' => false, 'error' => 'Fehlende Eingabewerte']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE rabatt SET key_name = :key_name, description = :description, rabatt_percent = :rabatt_percent WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':key_name' => $key_name,
        ':description' => $description,
        ':rabatt_percent' => $rabatt_percent
    ]);

    echo json_encode(['success' => true, 'message' => 'Rabatt erfolgreich bearbeitet']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Bearbeiten des Rabatts: ' . $e->getMessage()]);
}
?>
