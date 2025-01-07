<?php
require_once 'db.php'; // Deine DB-Verbindungsdatei

try {
    // Abrufen der Kategorien
    $sql = "SELECT * FROM ausruestungskategorien ORDER BY name";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($categories);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
}

$conn = null; // Verbindung schließen
?>
