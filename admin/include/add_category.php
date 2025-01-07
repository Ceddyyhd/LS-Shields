<?php
require_once 'db.php'; // Deine DB-Verbindungsdatei

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Empfangen der neuen Kategorie
    $newCategory = $_POST['new_category'];

    // SQL-Abfrage zum Einfügen der neuen Kategorie
    $sql = "INSERT INTO ausruestungstypen (category) VALUES (:new_category)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':new_category', $newCategory);

    try {
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Kategorie erfolgreich hinzugefügt.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Hinzufügen der Kategorie: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
}

$conn = null; // Verbindung schließen
?>
