<?php
require_once 'db.php';
session_start();
header('Content-Type: application/json');

try {
    // SQL-Abfrage, um alle Kategorien abzurufen
    $sql = "SELECT name FROM ausruestungskategorien"; // Abhängig von deiner Tabellenstruktur
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Alle Ergebnisse in einem Array speichern
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Header setzen, um die Antwort als JSON zurückzugeben
    echo json_encode($categories);

} catch (PDOException $e) {
    error_log('Datenbankfehler: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}

$conn = null;
?>
