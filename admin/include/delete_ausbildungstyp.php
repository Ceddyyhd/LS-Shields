<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

require_once 'db.php'; // Deine DB-Verbindungsdatei

// Überprüfen, ob eine ID übergeben wurde
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        // Daten in die Archiv-Tabelle verschieben
        $sql = "INSERT INTO ausbildungstypen_alt (id, key_name, display_name, description) SELECT id, key_name, display_name, description FROM ausbildungstypen WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ausführen der Einfüge-Abfrage
        $stmt->execute();

        // Datensatz aus der ursprünglichen Tabelle löschen
        $sqlDelete = "DELETE FROM ausbildungstypen WHERE id = :id";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
        
        // Löschen ausführen
        $stmtDelete->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Archivieren des Ausbildungstyps: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine ID angegeben.']);
}

$conn = null; // Verbindung schließen
?>
