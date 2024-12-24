<?php
include 'db.php';

header('Content-Type: application/json');

// Eingabedaten abrufen
$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? null;
$level = $_POST['level'] ?? null;
$permissions = json_decode($_POST['permissions'], true); // JSON-Daten dekodieren

// Eingabevalidierung
if (!$id || !$name || !$level || !is_array($permissions)) {
    echo json_encode(['success' => false, 'message' => 'Ungültige Eingaben.']);
    exit;
}

try {
    // Bestehende Rolle aktualisieren
    $stmt = $conn->prepare("UPDATE roles SET name = :name, level = :level, value = :value, permissions = :permissions WHERE id = :id");
    $stmt->execute([
        ':name' => $name,
        ':level' => $level,
        ':value' => $value, // Value einfügen
        ':permissions' => json_encode($permissions),
        ':id' => $id
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
