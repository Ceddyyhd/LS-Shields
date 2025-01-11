<?php
include 'db.php';
session_start();

header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

// Eingabedaten abrufen
$roleId = $_POST['id'] ?? null;
$name = $_POST['name'] ?? null;
$level = $_POST['level'] ?? null;
$value = $_POST['value'] ?? null;
$permissions = json_decode($_POST['permissions'], true); // JSON-Daten dekodieren

if (!$roleId || !$name || !$level || !$value || !is_array($permissions)) {
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
        echo json_encode(['success' => false, 'message' => 'Sie können keine Rolle mit einem höheren Wert bearbeiten als Ihre eigene Rolle.']);
        exit;
    }

    // Rolle in der Datenbank aktualisieren
    $stmt = $conn->prepare("UPDATE roles SET name = :name, level = :level, value = :value, permissions = :permissions WHERE id = :id");
    $stmt->execute([
        ':id' => $roleId,
        ':name' => $name,
        ':level' => $level,
        ':value' => $value,
        ':permissions' => json_encode($permissions)
    ]);

    // Log-Eintrag für das Bearbeiten
    logAction('UPDATE', 'roles', 'id: ' . $roleId . ', bearbeitet von: ' . $_SESSION['user_id']);

    echo json_encode(['success' => true, 'message' => 'Rolle erfolgreich bearbeitet']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Fehler beim Bearbeiten der Rolle: ' . $e->getMessage()]);
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
