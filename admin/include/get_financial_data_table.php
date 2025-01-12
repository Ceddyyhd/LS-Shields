<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include 'db.php'; // Deine PDO-Datenbankverbindung

// SQL-Abfragen zum Abrufen der Finanzdaten (z. B. Typ, Kategorie, Notiz, Betrag, erstellt_von)
$sql = "SELECT typ, kategorie, notiz, erstellt_von, betrag FROM finanzen";
$finanzen = [];

try {
    // Ausführen der SQL-Abfrage
    $stmt = $conn->query($sql);

    // Alle Daten abrufen
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $finanzen[] = $row;
    }

    // Rückgabe der Finanzdaten als JSON
    echo json_encode($finanzen);
} catch (PDOException $e) {
    // Fehlerbehandlung: Gebe eine Fehlermeldung zurück, wenn die Abfrage fehlschlägt
    echo json_encode(["status" => "error", "message" => "Fehler bei der Datenbankabfrage: " . $e->getMessage()]);
}

// Schließen der Verbindung
$conn = null;
?>
