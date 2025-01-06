<?php
include 'db.php';
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    if ($id) {
        $stmt = $conn->prepare("SELECT id, key_name, description, rabatt_percent FROM rabatt WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $rabatt = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rabatt) {
            echo json_encode([$rabatt]);
        } else {
            echo json_encode(['error' => 'Rabatt nicht gefunden']);
        }
    } else {
        $stmt = $conn->prepare("SELECT id, key_name, description, rabatt_percent FROM rabatt");
        $stmt->execute();
        $rabatte = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($rabatte);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Abrufen der Rabatte: ' . $e->getMessage()]);
}
?>
