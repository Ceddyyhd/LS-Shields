<?php
require_once 'db.php'; // Deine DB-Verbindungsdatei

try {
    // SQL-Abfrage, um alle Kategorien abzurufen
    $sql = "SELECT DISTINCT category FROM ausruestungstypen";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Alle Ergebnisse in einem Array speichern
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Header setzen, um die Antwort als JSON zurückzugeben
    header('Content-Type: application/json');
    echo json_encode($categories);

} catch (PDOException $e) {
    // Fehlerbehandlung
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}

$conn = null; // Verbindung schließen
?>
