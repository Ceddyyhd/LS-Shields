<?php
include 'db.php';  // Deine Datenbankverbindung

// SQL-Abfrage zum Abrufen aller Kategorien
$sql = "SELECT name FROM finanzen_kategorien";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Fehler bei der Abfrage: " . mysqli_error($conn));
}

// Alle Kategorien in ein Array laden
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

// JSON-Ausgabe der Kategorien
echo json_encode($categories);

// Schließen der Verbindung
mysqli_free_result($result);
mysqli_close($conn);
?>
