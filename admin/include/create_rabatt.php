<?php
include 'db.php';
header('Content-Type: application/json');

// Eingabewerte
$key_name = $_POST['key_name'] ?? null;
$description = $_POST['description'] ?? null;
$rabatt_percent = $_POST['rabatt_percent'] ?? null;
$created_by = $_POST['created_by'] ?? null;  // Jetzt aus dem Formular holen

if (!$key_name || !$description || !$rabatt_percent || !$created_by) {
    echo json_encode(['success' => false, 'error' => 'Fehlende Eingabewerte']);
    exit;
}

try {
    // Rabatt erstellen
    $stmt = $conn->prepare("INSERT INTO rabatt (key_name, description, rabatt_percent, created_by) VALUES (:key_name, :description, :rabatt_percent, :created_by)");
    $stmt->execute([
        ':key_name' => $key_name,
        ':description' => $description,
        ':rabatt_percent' => $rabatt_percent,
        ':created_by' => $created_by
    ]);
    $rabatt_id = $conn->lastInsertId();  // ID des neu erstellten Rabatts

    // Log-Eintrag für das Erstellen
    $logStmt = $conn->prepare("INSERT INTO rabatt_logs (rabatt_id, action, changed_by) VALUES (:rabatt_id, :action, :changed_by)");
    $logStmt->execute([
        ':rabatt_id' => $rabatt_id,
        ':action' => 'Erstellt',
        ':changed_by' => $created_by  // Der Benutzer, der den Rabatt erstellt hat
    ]);

    echo json_encode(['success' => true, 'message' => 'Rabatt erfolgreich erstellt']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Erstellen des Rabatts: ' . $e->getMessage()]);
}
?>
