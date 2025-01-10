<?php
// Datenbankverbindung einbinden
include 'db.php';

// Überprüfen, ob die location per POST gesendet wurde
if (isset($_POST['location'])) {
    $location = $_POST['location'];

    try {
        // SQL-Abfrage zum Löschen der Einträge mit der angegebenen Location
        $stmt = $conn->prepare("DELETE FROM deckel WHERE location = :location");
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->execute();
        
        // Erfolgsantwort zurückgeben
        echo json_encode(['success' => true, 'message' => 'Erfolgreich gelöscht']);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
} else {
    // Fehler, wenn keine Location übermittelt wurde
    echo json_encode(['success' => false, 'message' => 'Keine Location angegeben']);
}
?>
