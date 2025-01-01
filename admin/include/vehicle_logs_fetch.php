<?php
include 'db.php';

// SQL-Query, um die letzten 25 Logs zu holen
$sql_logs = "SELECT * FROM vehicle_logs ORDER BY timestamp DESC LIMIT 25";
$stmt = $conn->prepare($sql_logs);
$stmt->execute();

// Alle Logs holen
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Die Logs als JSON zurückgeben
echo json_encode($logs);
?>
