<?php
include 'db.php';

header('Content-Type: application/json');

// ID des Rangs aus der Anfrage abrufen
$roleId = $_GET['id'] ?? null;

if (!$roleId || !is_numeric($roleId)) {
    echo json_encode(['success' => false, 'error' => 'Ungültige ID.']);
    exit;
}

// Rang aus der Datenbank abrufen
$stmt = $conn->prepare("SELECT * FROM roles WHERE id = :id");
$stmt->execute([':id' => $roleId]);
$role = $stmt->fetch(PDO::FETCH_ASSOC);

if ($role) {
    echo json_encode([
        'success' => true,
        'role' => [
            'name' => $role['name'],
            'level' => $role['level'],
            'permissions' => json_decode($role['permissions'], true) // Rechte als Array
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Rang nicht gefunden.']);
}
?>
