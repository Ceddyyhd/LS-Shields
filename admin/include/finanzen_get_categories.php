<?php
include 'db.php';

// Abfrage zum Abrufen aller Kategorien
$sql = "SELECT name FROM finanzen_kategorien";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Fehler bei der Abfrage: " . mysqli_error($conn));
}

// Alle Ergebnisse in ein Array speichern
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

// Ausgabe als JSON
echo json_encode($categories);

// Schließen der Verbindung
mysqli_free_result($result);
mysqli_close($conn);
?>
