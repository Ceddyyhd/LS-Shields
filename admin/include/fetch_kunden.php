<?php
include('db.php');

// Alle Kunden abfragen
$stmt = $conn->prepare("SELECT * FROM Kunden");
$stmt->execute();
$kunden = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Daten als JSON zurückgeben
echo json_encode($kunden);
?>
