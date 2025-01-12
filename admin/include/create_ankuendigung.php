<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include 'db.php';
header('Content-Type: application/json');

// Eingabewerte
$key_name = $_POST['key_name'] ?? null;
$description = $_POST['description'] ?? null;
$prioritaet = $_POST['prioritaet'] ?? null;
$created_by = $_POST['created_by'] ?? $_SESSION['username'];  // Benutzernamen aus der Session holen oder den angegebenen Namen aus dem Formular

if (!$key_name || !$description || !$prioritaet) {
    echo json_encode(['success' => false, 'error' => 'Fehlende Eingabewerte']);
    exit;
}

try {
    // Ankündigung erstellen
    $stmt = $conn->prepare("INSERT INTO ankuendigung (key_name, display_name, description, prioritaet, created_by) VALUES (:key_name, :key_name, :description, :prioritaet, :created_by)");
    $stmt->execute([
        ':key_name' => $key_name,
        ':display_name' => $key_name, // display_name ist in diesem Fall gleich key_name
        ':description' => $description,
        ':prioritaet' => $prioritaet,
        ':created_by' => $created_by  // Setze den Ersteller
    ]);

    // Log-Eintrag für das Erstellen
    $ankuendigung_id = $conn->lastInsertId();
    $logStmt = $conn->prepare("INSERT INTO ankuendigung_logs (ankuendigung_id, action, changed_by) VALUES (:ankuendigung_id, :action, :changed_by)");
    $logStmt->execute([
        ':ankuendigung_id' => $ankuendigung_id,
        ':action' => 'Erstellt',
        ':changed_by' => $_SESSION['username']  // Der Benutzer, der die Ankündigung erstellt hat, wird hier festgehalten
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Ankündigung erfolgreich erstellt']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Erstellen der Ankündigung: ' . $e->getMessage()]);
}
?>
