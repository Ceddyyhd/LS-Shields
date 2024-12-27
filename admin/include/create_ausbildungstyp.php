<?php
require_once 'db.php'; // Deine DB-Verbindungsdatei

// Sicherstellen, dass alle erforderlichen Felder vorhanden sind
if (isset($_POST['key_name']) && isset($_POST['display_name']) && isset($_POST['description'])) {
    $key_name = $_POST['key_name'];
    $display_name = $_POST['display_name'];
    $description = $_POST['description'];

    try {
        // SQL-Abfrage zum Einfügen eines neuen Ausbildungstyps
        $sql = "INSERT INTO ausbildungstypen (key_name, display_name, description) VALUES (:key_name, :display_name, :description)";
        $stmt = $conn->prepare($sql);
        
        // Binden der Parameter
        $stmt->bindParam(':key_name', $key_name);
        $stmt->bindParam(':display_name', $display_name);
        $stmt->bindParam(':description', $description);

        // Ausführen der Anfrage
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Erstellen des Ausbildungstyps.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Bitte alle Felder ausfüllen.']);
}
?>
