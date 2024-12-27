<?php
// Verbindung zur Datenbank herstellen
require_once 'db.php'; // Stelle sicher, dass du die korrekte Datenbankverbindungsdatei verwendest

// SQL-Abfrage, um alle Ausbildungstypen abzurufen
$sql = "SELECT id, key_name, display_name, description FROM ausbildungstypen";
$result = $conn->query($sql);

$ausbildungstypen = [];
if ($result->num_rows > 0) {
    // Jede Zeile der Ergebnistabelle durchlaufen
    while ($row = $result->fetch_assoc()) {
        $ausbildungstypen[] = $row;
    }
}

// Header setzen, um die Antwort als JSON zurückzugeben
header('Content-Type: application/json');
echo json_encode($ausbildungstypen);

// Verbindung schließen
$conn->close();
?>
