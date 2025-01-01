<?php
include 'db.php';

// SQL-Query, um die letzten 25 Logs zu holen
$sql_logs = "SELECT * FROM vehicles_logs ORDER BY timestamp DESC LIMIT 25";
$stmt = $conn->prepare($sql_logs);

try {
    $stmt->execute();

    // Alle Logs holen
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Header für JSON setzen
    header('Content-Type: application/json');

    // Die Logs als JSON zurückgeben
    echo json_encode($logs);

} catch (PDOException $e) {
    // Fehlerbehandlung
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
