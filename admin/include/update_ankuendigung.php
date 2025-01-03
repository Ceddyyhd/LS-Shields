<?php
include 'db.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$key_name = $_POST['key_name'] ?? null;
$display_name = $_POST['display_name'] ?? null;
$description = $_POST['description'] ?? null;
$prioritaet = $_POST['prioritaet'] ?? null;

if (!$id || !$key_name || !$display_name || !$prioritaet) {
    echo json_encode(['success' => false, 'error' => 'Fehlende Eingabewerte']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE ankuendigung SET key_name = :key_name, display_name = :display_name, description = :description, prioritaet = :prioritaet WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':key_name' => $key_name,
        ':display_name' => $display_name,
        ':description' => $description,
        ':prioritaet' => $prioritaet
    ]);

    echo json_encode(['success' => true, 'message' => 'Ankündigung erfolgreich bearbeitet']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Bearbeiten der Ankündigung: ' . $e->getMessage()]);
}
?>
