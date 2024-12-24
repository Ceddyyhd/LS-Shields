<?php
include 'db.php';

header('Content-Type: application/json');

// Eingabedaten abrufen
$name = $_POST['name'] ?? null;
$level = $_POST['level'] ?? null;
$value = $_POST['value'] ?? null;
$permissions = json_decode($_POST['permissions'], true);

// Eingaben validieren
if (!$name || !$level || !$value || !is_array($permissions)) {
    echo json_encode(['success' => false, 'message' => 'Ungültige Eingaben.']);
    exit;
}

// Überprüfen, ob der Benutzer die Berechtigung hat, Rollen mit diesem Wert zu erstellen
session_start();
$current_user_value = $_SESSION['user_value'] ?? null; // Aktueller Wert des eingeloggten Benutzers
if ($current_user_value === null || $value > $current_user_value) {
    echo json_encode(['success' => false, 'message' => 'Sie können keine Rollen mit einem höheren Rang als Ihrem erstellen.']);
    exit;
}

$value = $_POST['value'] ?? null;
if (!$value || !is_numeric($value)) {
    echo json_encode(['success' => false, 'message' => 'Ungültiger Value.']);
    exit;
}

try {
    // Rolle in die Datenbank einfügen
    $stmt = $conn->prepare("INSERT INTO roles (name, level, value, permissions) VALUES (:name, :level, :value, :permissions)");
    $stmt->execute([
        ':name' => $name,
        ':level' => $level,
        ':value' => $value,
        ':permissions' => json_encode($permissions)
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}

?>
