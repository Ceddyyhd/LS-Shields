<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

require_once 'db.php'; // Deine DB-Verbindungsdatei

// Überprüfen, ob alle erforderlichen Daten übermittelt wurden
if (isset($_POST['key_name']) && isset($_POST['display_name']) && isset($_POST['category']) && isset($_POST['description'])) {
    $key_name = $_POST['key_name'];
    $display_name = $_POST['display_name'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    try {
        // SQL-Abfrage zum Einfügen eines neuen Ausrüstungstyps
        $sql = "INSERT INTO ausruestungstypen (key_name, display_name, category, description) VALUES (:key_name, :display_name, :category, :description)";
        $stmt = $conn->prepare($sql);

        // Parameter binden
        $stmt->bindParam(':key_name', $key_name);
        $stmt->bindParam(':display_name', $display_name);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':description', $description);

        // Ausführen der Anfrage
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Erstellen des Ausrüstungstyps.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Fehlende Daten.']);
}

$conn = null; // Verbindung schließen
?>
