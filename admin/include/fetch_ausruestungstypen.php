<?php
require_once 'db.php'; // Deine DB-Verbindungsdatei



try {
    // SQL-Abfrage, um alle Ausrüstungstypen abzurufen
    $sql = "SELECT id, key_name, display_name, category, description FROM ausruestungstypen";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $ausruestungstypen = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Header setzen, um die Antwort als JSON zurückzugeben
    header('Content-Type: application/json');
    echo json_encode($ausruestungstypen);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}

$conn = null; // Verbindung schließen
?>
