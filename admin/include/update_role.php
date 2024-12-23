<?php
// Debugging: Alle Fehler anzeigen
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

header('Content-Type: application/json');

// POST-Daten abrufen
$roleId = $_POST['id'] ?? null;
$name = $_POST['name'] ?? null;
$level = $_POST['level'] ?? null;
$permissions = $_POST['permissions'] ?? null;

if (!$roleId || !is_numeric($roleId) || !$name || !$level || !$permissions) {
    echo json_encode(['success' => false, 'message' => 'Ungültige Eingaben.']);
    exit;
}

try {
    // Berechtigungen validieren und als JSON speichern
    $permissionsJson = json_encode(json_decode($permissions, true));

    if (!$permissionsJson) {
        echo json_encode(['success' => false, 'message' => 'Ungültige Berechtigungen.']);
        exit;
    }

    // Rollendaten aktualisieren
    $stmt = $conn->prepare("
        UPDATE roles 
        SET name = :name, level = :level, permissions = :permissions 
        WHERE id = :id
    ");
    $stmt->execute([
        ':name' => $name,
        ':level' => $level,
        ':permissions' => $permissionsJson,
        ':id' => $roleId
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
