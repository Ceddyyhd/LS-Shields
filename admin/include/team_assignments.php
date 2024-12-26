<?php
include('db.php');

// ID des Events (dies sollte aus der URL oder anderweitig kommen)
$eventId = 1; // Beispiel-ID, falls die ID aus der URL kommt, kannst du sie mit $_GET['id'] holen

// SQL-Abfrage, um die gespeicherten Team-Daten zu erhalten
$stmt = $conn->prepare("SELECT team_verteilung FROM eventplanung WHERE id = :id");
$stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
$stmt->execute();

// Das Ergebnis abrufen
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// JSON-Daten aus der DB holen
if ($result) {
    $teamData = json_decode($result['team_verteilung'], true);  // Umwandeln in ein PHP-Array
    if ($teamData === null) {
        echo "Fehler beim Decodieren der JSON-Daten.";
        exit;
    }
    // Ausgabe der Daten zum Debuggen
    echo "<pre>";
    print_r($teamData);  // Gibt die Teams als Array aus
    echo "</pre>";
} else {
    echo "Kein Team gefunden.";
}
?>
