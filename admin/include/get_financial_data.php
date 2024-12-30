<?php
include 'db.php'; // Deine PDO-Datenbankverbindung

// SQL-Abfragen zum Berechnen der Einnahmen und Ausgaben
$sql_einnahmen = "SELECT SUM(betrag) AS einnahmen FROM finanzen WHERE typ = 'Einnahme'";
$sql_ausgaben = "SELECT SUM(betrag) AS ausgaben FROM finanzen WHERE typ = 'Ausgabe'";

try {
    // Berechne Einnahmen
    $stmt_einnahmen = $conn->query($sql_einnahmen);
    $einnahmen = $stmt_einnahmen->fetch(PDO::FETCH_ASSOC)['einnahmen'];

    // Berechne Ausgaben
    $stmt_ausgaben = $conn->query($sql_ausgaben);
    $ausgaben = $stmt_ausgaben->fetch(PDO::FETCH_ASSOC)['ausgaben'];

    // Wenn die Werte NULL sind, setze sie auf 0
    $einnahmen = $einnahmen ?? 0;
    $ausgaben = $ausgaben ?? 0;

    // Kontostand berechnen
    $kontostand = $einnahmen - $ausgaben;

    // Rückgabe der Daten als JSON
    echo json_encode([
        'kontostand' => $kontostand,
        'einnahmen' => $einnahmen,
        'ausgaben' => $ausgaben
    ]);
} catch (PDOException $e) {
    // Fehlerbehandlung: Gebe eine Fehlermeldung zurück, wenn die Datenbankabfragen fehlschlagen
    echo json_encode(["status" => "error", "message" => "Fehler bei der Datenbankabfrage: " . $e->getMessage()]);
}
?>
