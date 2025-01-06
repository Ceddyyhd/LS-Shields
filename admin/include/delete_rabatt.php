<?php
include 'db.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Fehlende ID']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM rabatt WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode(['success' => true, 'message' => 'Rabatt erfolgreich gelöscht']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Löschen des Rabatts: ' . $e->getMessage()]);
}
?>
