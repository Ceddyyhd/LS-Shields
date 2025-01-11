<?php
include 'db.php';
session_start();
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$key_name = $_POST['key_name'] ?? null;
$display_name = $_POST['display_name'] ?? null;
$description = $_POST['description'] ?? null;
$prioritaet = $_POST['prioritaet'] ?? null;
$updated_by = $_SESSION['username'] ?? 'Unbekannt';  // Beispiel: Benutzernamen aus der Session holen

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

if (!$id || !$key_name || !$description || !$prioritaet) {
    echo json_encode(['success' => false, 'error' => 'Fehlende Eingabewerte']);
    exit;
}

// Setze display_name auf key_name, wenn display_name nicht übergeben wird
$display_name = $display_name ?? $key_name;

try {
    $stmt = $conn->prepare("UPDATE ankuendigung SET key_name = :key_name, display_name = :display_name, description = :description, prioritaet = :prioritaet WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':key_name' => $key_name,
        ':display_name' => $display_name,
        ':description' => $description,
        ':prioritaet' => $prioritaet
    ]);

    // Log-Eintrag für das Bearbeiten
    logAction('UPDATE', 'ankuendigung', 'id: ' . $id . ', bearbeitet von: ' . $updated_by);

    echo json_encode(['success' => true, 'message' => 'Ankündigung erfolgreich bearbeitet']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Bearbeiten der Ankündigung: ' . $e->getMessage()]);
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
?>
