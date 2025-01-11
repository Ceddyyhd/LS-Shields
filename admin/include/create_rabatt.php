<?php
include 'db.php';
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);


// Eingabewerte
$display_name = $_POST['display_name'] ?? null; // 'key_name' durch 'display_name' ersetzt
$description = $_POST['description'] ?? null;
$rabatt_percent = $_POST['rabatt_percent'] ?? null;
$created_by = $_POST['created_by'] ?? null;

if (!$display_name || !$description || !$rabatt_percent || !$created_by) {
    echo json_encode(['success' => false, 'error' => 'Fehlende Eingabewerte']);
    exit;
}

try {
    // Rabatt erstellen
    $stmt = $conn->prepare("INSERT INTO rabatt (display_name, description, rabatt_percent, created_by) 
                        VALUES (:display_name, :description, :rabatt_percent, :created_by)");
    $stmt->execute([
        ':display_name' => $display_name,
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
