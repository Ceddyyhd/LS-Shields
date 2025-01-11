<?php
// Verbindung zur Datenbank herstellen
require_once 'db.php'; // Deine DB-Verbindungsdatei
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

try {
    // SQL-Abfrage, um alle Ausbildungstypen abzurufen
    $sql = "SELECT id, key_name, display_name, description FROM ausbildungstypen";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Alle Ergebnisse in einem Array speichern
    $ausbildungstypen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Header setzen, um die Antwort als JSON zurückzugeben
    echo json_encode($ausbildungstypen);

} catch (PDOException $e) {
    // Fehlerbehandlung
    error_log('Datenbankfehler: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}

// Verbindung schließen
$conn = null;
?>