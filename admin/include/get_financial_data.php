<?php
include 'db.php'; // Deine Datenbankverbindung

// Berechne Einnahmen und Ausgaben
$sql_einnahmen = "SELECT SUM(betrag) AS einnahmen FROM finanzen WHERE typ = 'Einnahme'";
$sql_ausgaben = "SELECT SUM(betrag) AS ausgaben FROM finanzen WHERE typ = 'Ausgabe'";

// Führt die SQL-Abfragen aus
$result_einnahmen = mysqli_query($conn, $sql_einnahmen);
$result_ausgaben = mysqli_query($conn, $sql_ausgaben);

// Holen der Summen
$einnahmen = mysqli_fetch_assoc($result_einnahmen)['einnahmen'];
$ausgaben = mysqli_fetch_assoc($result_ausgaben)['ausgaben'];

// Wenn die Werte NULL sind, setze sie auf 0
$einnahmen = $einnahmen ?? 0;
$ausgaben = $ausgaben ?? 0;

// Kontostand berechnen
$kontostand = $einnahmen - $ausgaben;

// Rückgabe als JSON
echo json_encode([
    'kontostand' => $kontostand,
    'einnahmen' => $einnahmen,
    'ausgaben' => $ausgaben
]);
?>
