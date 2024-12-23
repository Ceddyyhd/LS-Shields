<?php
include 'db.php';

header('Content-Type: application/json');

// ID des Rangs aus der Anfrage abrufen
$roleId = $_GET['id'] ?? null;

if ($roleId) {
    // Rang aus der Datenbank abrufen
    $stmt = $conn->prepare("SELECT * FROM roles WHERE id = :id");
    $stmt->execute([':id' => $roleId]);
    $role = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($role) {
        echo json_encode([
            'name' => $role['name'],
            'level' => $role['level'],
            'permissions' => json_decode($role['permissions'], true) // Rechte als Array
        ]);
    } else {
        echo json_encode(['error' => 'Rang nicht gefunden.']);
    }
} else {
    echo json_encode(['error' => 'Ungültige Anfrage.']);
}
?>
