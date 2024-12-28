<?php
require_once 'db.php'; // Deine DB-Verbindungsdatei

if (isset($_POST['id']) && isset($_POST['key_name']) && isset($_POST['display_name']) && isset($_POST['category']) && isset($_POST['description'])) {
    $id = $_POST['id'];
    $key_name = $_POST['key_name'];
    $display_name = $_POST['display_name'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    try {
        // SQL-Abfrage zum Aktualisieren eines Ausrüstungstyps
        $sql = "UPDATE ausruestungstypen SET key_name = :key_name, display_name = :display_name, category = :category, description = :description WHERE id = :id";
        $stmt = $conn->prepare($sql);

        // Parameter binden
        $stmt->bindParam(':key_name', $key_name);
        $stmt->bindParam(':display_name', $display_name);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ausführen der Anfrage
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren des Ausrüstungstyps.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Fehlende Daten.']);
}

$conn = null; // Verbindung schließen
?>
