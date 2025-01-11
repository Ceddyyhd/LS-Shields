<?php
// Verbindung zur Datenbank herstellen
require_once 'db.php'; // Deine DB-Verbindungsdatei
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

$id = $_POST['id'] ?? null;
$key_name = $_POST['key_name'] ?? null;
$display_name = $_POST['display_name'] ?? null;
$description = $_POST['description'] ?? null;

if (!$id || !$key_name || !$description) {
    echo json_encode(['success' => false, 'message' => 'Fehlende Eingabewerte']);
    exit;
}

// Setze display_name auf key_name, wenn display_name nicht übergeben wird
$display_name = $display_name ?? $key_name;

try {
    // SQL-Abfrage zum Aktualisieren des Ausbildungstyps
    $stmt = $conn->prepare("UPDATE ausbildungstypen SET key_name = :key_name, display_name = :display_name, description = :description WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':key_name' => $key_name,
        ':display_name' => $display_name,
        ':description' => $description
    ]);

    // Log-Eintrag für das Bearbeiten
    logAction('UPDATE', 'ausbildungstypen', 'id: ' . $id . ', bearbeitet von: ' . $_SESSION['user_id']);

    echo json_encode(['success' => true, 'message' => 'Ausbildungstyp erfolgreich bearbeitet']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Fehler beim Bearbeiten des Ausbildungstyps: ' . $e->getMessage()]);
}

// Funktion zum Loggen von Aktionen
function logAction($action, $table, $details) {
    global $conn;

    // SQL-Abfrage zum Einfügen des Log-Eintrags
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, details, user_id, timestamp) VALUES (:action, :table_name, :details, :user_id, NOW())");
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':table_name', $table, PDO::PARAM_STR);
    $stmt->bindParam(':details', $details, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
}

// Verbindung schließen
$conn = null;
?>