<?php
require_once 'db.php'; // Deine DB-Verbindungsdatei

// Überprüfen, ob die erforderlichen Daten übermittelt wurden
if (isset($_POST['id']) && isset($_POST['key_name']) && isset($_POST['display_name']) && isset($_POST['description'])) {
    $id = $_POST['id'];
    $key_name = $_POST['key_name'];
    $display_name = $_POST['display_name'];
    $description = $_POST['description'];

    try {
        // SQL-Abfrage zum Aktualisieren eines Ausbildungstyps
        $sql = "UPDATE ausbildungstypen SET key_name = :key_name, display_name = :display_name, description = :description WHERE id = :id";
        $stmt = $conn->prepare($sql);

        // Parameter binden
        $stmt->bindParam(':key_name', $key_name);
        $stmt->bindParam(':display_name', $display_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ausführen der Anfrage
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren des Ausbildungstyps.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Fehlende Daten.']);
}

$conn = null; // Verbindung schließen
?>
