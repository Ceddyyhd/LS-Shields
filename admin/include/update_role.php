<?php
include 'db.php';
header('Content-Type: application/json');

// POST-Daten abrufen
$roleId = $_POST['id'] ?? null;
$name = $_POST['name'] ?? null;
$level = $_POST['level'] ?? null;
$permissions = $_POST['permissions'] ?? null;

if (!$roleId || !$name || !$level || !$permissions) {
    echo json_encode(['success' => false, 'message' => 'Ungültige Eingaben.']);
    exit;
}

// Berechtigungen als JSON speichern
$stmt = $conn->prepare("UPDATE roles SET name = :name, level = :level, permissions = :permissions WHERE id = :id");
$stmt->execute([
    ':name' => $name,
    ':level' => $level,
    ':permissions' => $permissions,
    ':id' => $roleId
]);

echo json_encode(['success' => true]);
?>
