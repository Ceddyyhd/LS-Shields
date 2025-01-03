<?php
include 'db.php';
header('Content-Type: application/json');

try {
    // Holen der Ankündigungen inklusive 'created_by' (und anderer Daten)
    $stmt = $conn->prepare("SELECT id, key_name, display_name, description, prioritaet, created_by FROM ankuendigung");
    $stmt->execute();
    $ankuendigungen = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Gebe die Ankündigungen als JSON zurück
    echo json_encode($ankuendigungen);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Abrufen der Ankündigungen: ' . $e->getMessage()]);
}
?>
