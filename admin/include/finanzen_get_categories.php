<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include 'db.php';  // Deine PDO-Datenbankverbindung

// SQL-Abfrage zum Abrufen aller Kategorien
$sql = "SELECT name FROM finanzen_kategorien";

try {
    // Ausführen der SQL-Abfrage
    $stmt = $conn->query($sql);

    // Alle Kategorien in ein Array laden
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON-Ausgabe der Kategorien
    echo json_encode($categories);
} catch (PDOException $e) {
    // Fehlerbehandlung, falls die Abfrage fehlschlägt
    echo json_encode(["status" => "error", "message" => "Fehler bei der Abfrage: " . $e->getMessage()]);
}

// Schließen der Verbindung
$conn = null;
?>
