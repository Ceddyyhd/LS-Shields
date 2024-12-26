<?php
include('db.php');

// ID des Events (kann aus der URL oder anderweitig kommen)
$eventId = 1;  // Beispielwert

// SQL-Abfrage, um die gespeicherten Team-Daten zu erhalten
$stmt = $conn->prepare("SELECT team_verteilung FROM eventplanung WHERE id = :id");
$stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
$stmt->execute();

// Das Ergebnis abrufen
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// JSON-Daten aus der DB holen
if ($result) {
    $teamData = json_decode($result['team_verteilung'], true);  // Umwandeln in ein PHP-Array
    print_r($teamData);  // Ausgabe der Daten zum Debuggen
} else {
    echo "Kein Team gefunden.";
}
?>
