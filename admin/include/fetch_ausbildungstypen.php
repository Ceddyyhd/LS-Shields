<?php
// Verbindung zur Datenbank herstellen
require_once 'db.php'; // Deine DB-Verbindungsdatei

// Überprüfen, ob eine ID übergeben wurde
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // SQL-Abfrage, um einen Ausbildungstyp nach ID abzurufen
        $sql = "SELECT id, key_name, display_name, description FROM ausbildungstypen WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Ergebnis zurückgeben
        $ausbildungstyp = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Wenn der Ausbildungstyp existiert, gebe ihn zurück
        if ($ausbildungstyp) {
            echo json_encode([$ausbildungstyp]);
        } else {
            echo json_encode([]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine ID angegeben.']);
}

$conn = null; // Verbindung schließen
?>
