<?php
include 'db.php';
header('Content-Type: application/json');

try {
    $stmt = $conn->prepare("SELECT * FROM ankuendigung");
    $stmt->execute();
    $ankuendigungen = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($ankuendigungen);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Abrufen der Ankündigungen: ' . $e->getMessage()]);
}
?>
