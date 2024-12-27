<?php
// Verbindung zur Datenbank herstellen
require_once 'db.php'; // Stelle sicher, dass du die korrekte Datenbankverbindungsdatei verwendest

// Überprüfen, ob eine ID übergeben wurde
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // SQL-Abfrage zum Löschen des Ausbildungstyps
    $sql = "DELETE FROM ausbildungstypen WHERE id = ?";

    // Vorbereiten der SQL-Anweisung
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Erfolgreiches Löschen
        echo json_encode(['success' => true]);
    } else {
        // Fehler beim Löschen
        echo json_encode(['success' => false, 'message' => 'Fehler beim Löschen des Ausbildungstyps.']);
    }

    // Verbindung schließen
    $stmt->close();
    $conn->close();
} else {
    // Fehlende ID
    echo json_encode(['success' => false, 'message' => 'Keine ID angegeben.']);
}
?>
