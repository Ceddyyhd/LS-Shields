<?php
// Debugging: Alle Fehler anzeigen
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

header('Content-Type: application/json');

// ID des Rangs aus der Anfrage abrufen
$roleId = $_GET['id'] ?? null;

if (!$roleId || !is_numeric($roleId)) {
    echo json_encode(['success' => false, 'error' => 'Ungültige ID.']);
    exit;
}

try {
    // Rang aus der Datenbank abrufen
    $stmt = $conn->prepare("SELECT * FROM roles WHERE id = :id");
    $stmt->execute([':id' => $roleId]);
    $role = $stmt->fetch(PDO::FETCH_ASSOC);

    // Berechtigungen aus der Datenbank abrufen
    $stmtPerm = $conn->prepare("SELECT * FROM permissions");
    $stmtPerm->execute();
    $permissions = $stmtPerm->fetchAll(PDO::FETCH_ASSOC);

    if ($role) {
        echo json_encode([
            'success' => true,
            'role' => [
                'name' => $role['name'],
                'level' => $role['level'],
                'value' => $role['value'], // Hier wird der Wert hinzugefügt
                'permissions' => json_decode($role['permissions'], true) ?? [] // Rechte als Array
            ],
            'all_permissions' => $permissions // Alle Berechtigungen
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Rang nicht gefunden.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
