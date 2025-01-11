<?php
include 'db.php';
session_start();

header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: ../error.php');
    exit;
}

// Eingabedaten abrufen
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$level = filter_input(INPUT_POST, 'level', FILTER_VALIDATE_INT);
$value = filter_input(INPUT_POST, 'value', FILTER_VALIDATE_INT);
$permissions = json_decode($_POST['permissions'], true); // JSON-Daten dekodieren

if (!$name || !$level || !$value || !is_array($permissions)) {
    echo json_encode(['success' => false, 'message' => 'Ungültige Eingaben.']);
    exit;
}

try {
    // Hole den Wert des eingeloggten Benutzers
    $stmt = $conn->prepare("SELECT roles.value FROM roles 
                            JOIN users ON users.role_id = roles.id 
                            WHERE users.id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $currentUserRole = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentUserRole) {
        echo json_encode(['success' => false, 'message' => 'Benutzerrolle nicht gefunden.']);
        exit;
    }

    $currentUserValue = (int) $currentUserRole['value'];

    // Überprüfen, ob der neue Wert den Wert des aktuellen Benutzers überschreitet
    if ($value > $currentUserValue) {
        echo json_encode(['success' => false, 'message' => 'Sie können keine Rolle mit einem höheren Wert erstellen als Ihre eigene Rolle.']);
        exit;
    }

    // Rolle in die Datenbank einfügen
    $stmt = $conn->prepare("INSERT INTO roles (name, level, value, permissions) VALUES (:name, :level, :value, :permissions)");
    $stmt->execute([
        ':name' => $name,
        ':level' => $level,
        ':value' => $value,
        ':permissions' => json_encode($permissions)
    ]);

    // Loggen des Eintrags
    logAction('INSERT', 'roles', 'name: ' . $name . ', level: ' . $level . ', value: ' . $value);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('Database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    exit;
}

// Funktion zum Loggen von Aktionen
function logAction($action, $table, $details) {
    global $conn;

    // SQL-Abfrage zum Einfügen des Log-Eintrags
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, details, user_id, timestamp) VALUES (:action, :table_name, :details, :user_id, NOW())");
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':table_name', $table, PDO::PARAM_STR);
    $stmt->bindParam(':details', $details, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
}
?>
