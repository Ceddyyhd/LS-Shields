<?php
include 'db.php';
header('Content-Type: application/json');

// Überprüfe, ob eine ID in der Anfrage enthalten ist
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($id) {
    try {
        // Holen der Ankündigung mit der entsprechenden ID
        $stmt = $conn->prepare("SELECT id, key_name, display_name, description, prioritaet, created_by FROM ankuendigung WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $ankuendigung = $stmt->fetch(PDO::FETCH_ASSOC);

        // Wenn Ankündigung gefunden wurde, zurückgeben
        if ($ankuendigung) {
            echo json_encode([$ankuendigung]);
        } else {
            echo json_encode(['error' => 'Ankündigung nicht gefunden']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Fehler beim Abrufen der Ankündigung: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Keine ID angegeben']);
}
?>
