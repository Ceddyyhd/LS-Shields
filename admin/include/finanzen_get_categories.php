<?php
include 'db.php';  // Deine PDO-Datenbankverbindung
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

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
    error_log('Fehler bei der Abfrage: ' . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Fehler bei der Abfrage: " . $e->getMessage()]);
}

// Schließen der Verbindung
$conn = null;
?>
