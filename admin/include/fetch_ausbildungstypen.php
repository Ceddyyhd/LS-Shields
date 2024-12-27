<?php
// Verbindung zur Datenbank herstellen
require_once 'db.php'; // Deine DB-Verbindungsdatei

try {
    // SQL-Abfrage, um alle Ausbildungstypen abzurufen
    $sql = "SELECT id, key_name, display_name, description FROM ausbildungstypen";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Alle Ergebnisse in einem Array speichern
    $ausbildungstypen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Header setzen, um die Antwort als JSON zurückzugeben
    header('Content-Type: application/json');
    echo json_encode($ausbildungstypen);

} catch (PDOException $e) {
    // Fehlerbehandlung
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}

// Verbindung schließen
$conn = null;
?>
