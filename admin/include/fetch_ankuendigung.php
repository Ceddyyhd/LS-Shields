<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include 'db.php';
header('Content-Type: application/json');

// Überprüfe, ob eine ID in der Anfrage enthalten ist
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    if ($id) {
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
    } else {
        // Wenn keine ID übergeben wird, alle Ankündigungen abrufen
        $stmt = $conn->prepare("SELECT id, key_name, display_name, description, prioritaet, created_by FROM ankuendigung");
        $stmt->execute();
        $ankuendigungen = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Gebe alle Ankündigungen als JSON zurück
        echo json_encode($ankuendigungen);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Abrufen der Ankündigungen: ' . $e->getMessage()]);
}
?>
