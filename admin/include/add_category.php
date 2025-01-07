<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newCategoryName = $_POST['new_category_name'];

    // Verhindern, dass eine doppelte Kategorie hinzugefügt wird
    $sql = "INSERT INTO ausruestungskategorien (name) VALUES (:name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $newCategoryName);

    try {
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Kategorie erfolgreich hinzugefügt.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Hinzufügen der Kategorie: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
}

$conn = null;
?>
