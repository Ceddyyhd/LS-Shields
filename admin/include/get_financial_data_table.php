<?php
include 'db.php'; // Deine PDO-Datenbankverbindung
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

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
    error_log('Fehler bei der Datenbankabfrage: ' . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Fehler bei der Datenbankabfrage: " . $e->getMessage()]);
}

// Schließen der Verbindung
$conn = null;
?>
