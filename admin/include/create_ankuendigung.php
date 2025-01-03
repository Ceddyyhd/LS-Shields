<?php
include 'db.php';
header('Content-Type: application/json');

// Eingabewerte
$key_name = $_POST['key_name'] ?? null;
$description = $_POST['description'] ?? null;
$prioritaet = $_POST['prioritaet'] ?? null;

if (!$key_name || !$description || !$prioritaet) {
    echo json_encode(['success' => false, 'error' => 'Fehlende Eingabewerte']);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO ankuendigung (key_name, display_name, description, prioritaet) VALUES (:key_name, :key_name, :description, :prioritaet)");
    $stmt->execute([
        ':key_name' => $key_name,
        ':display_name' => $key_name, // display_name ist in diesem Fall gleich key_name
        ':description' => $description,
        ':prioritaet' => $prioritaet
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Ankündigung erfolgreich erstellt']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Erstellen der Ankündigung: ' . $e->getMessage()]);
}
?>
