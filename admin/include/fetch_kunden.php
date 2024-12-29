<?php
include('db.php');

// Abfrage, um alle Kunden zu holen
$stmt = $conn->prepare("SELECT * FROM Kunden");
$stmt->execute();
$kunden = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ausgabe der Kunden
foreach ($kunden as $kunde) {
    echo "ID: " . htmlspecialchars($kunde['id']) . "<br>";
    echo "Name: " . htmlspecialchars($kunde['name']) . "<br>";
    echo "E-Mail: " . htmlspecialchars($kunde['umail']) . "<br>";
    echo "Status: " . htmlspecialchars($kunde['geloescht']) . "<br>";
    echo "<hr>";
}
?>
