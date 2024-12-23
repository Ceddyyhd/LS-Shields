<?php
include 'db.php';

header('Content-Type: application/json');

// Daten abrufen
$name = $_POST['name'] ?? '';
$level = $_POST['level'] ?? '';

if ($name && $level) {
    $stmt = $conn->prepare("INSERT INTO roles (name, level, permissions) VALUES (:name, :level, '{}')");
    try {
        $stmt->execute([':name' => $name, ':level' => $level]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Eingaben.']);
}
