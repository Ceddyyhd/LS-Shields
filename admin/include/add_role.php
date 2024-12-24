<?php
include 'db.php';

header('Content-Type: application/json');

// Eingabedaten abrufen
$name = $_POST['name'] ?? null;
$level = $_POST['level'] ?? null;
$permissions = json_decode($_POST['permissions'], true); // JSON-Daten dekodieren

if (!$name || !$level || !is_array($permissions)) {
    echo json_encode(['success' => false, 'message' => 'Ungültige Eingaben.']);
    exit;
}

try {
    // Rolle in die Datenbank einfügen
    $stmt = $conn->prepare("INSERT INTO roles (name, level, value, permissions) VALUES (:name, :level, :value, :permissions)");
    $stmt->execute([
        ':name' => $name,
        ':level' => $level,
        ':value' => $value, // Value einfügen
        ':permissions' => json_encode($permissions)
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
