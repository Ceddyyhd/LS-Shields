<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include 'db.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$key_name = $_POST['key_name'] ?? null;
$display_name = $_POST['display_name'] ?? null;
$description = $_POST['description'] ?? null;
$prioritaet = $_POST['prioritaet'] ?? null;
$updated_by = $_SESSION['username'] ?? 'Unbekannt';  // Beispiel: Benutzernamen aus der Session holen

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
    $logStmt = $conn->prepare("INSERT INTO ankuendigung_logs (ankuendigung_id, action, changed_by) VALUES (:ankuendigung_id, :action, :changed_by)");
    $logStmt->execute([
        ':ankuendigung_id' => $id,
        ':action' => 'Bearbeitet',
        ':changed_by' => $updated_by
    ]);

    echo json_encode(['success' => true, 'message' => 'Ankündigung erfolgreich bearbeitet']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Bearbeiten der Ankündigung: ' . $e->getMessage()]);
}
?>
