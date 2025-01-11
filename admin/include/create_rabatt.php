<?php
include 'db.php';
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: ../error.php');
    exit;
}

// Eingabewerte
$display_name = filter_input(INPUT_POST, 'display_name', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$rabatt_percent = filter_input(INPUT_POST, 'rabatt_percent', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$created_by = $_SESSION['user_id'] ?? null;

if (!$display_name || !$description || !$rabatt_percent || !$created_by) {
    echo json_encode(['success' => false, 'error' => 'Fehlende Eingabewerte']);
    exit;
}

try {
    // Rabatt erstellen
    $stmt = $conn->prepare("INSERT INTO rabatt (display_name, description, rabatt_percent, created_by) 
                        VALUES (:display_name, :description, :rabatt_percent, :created_by)");
    $stmt->execute([
        ':display_name' => $display_name,
        ':description' => $description,
        ':rabatt_percent' => $rabatt_percent,
        ':created_by' => $created_by
    ]);
    $rabatt_id = $conn->lastInsertId();  // ID des neu erstellten Rabatts

    // Log-Eintrag für das Erstellen
    logAction('INSERT', 'rabatt', 'rabatt_id: ' . $rabatt_id . ', created_by: ' . $created_by);

    echo json_encode(['success' => true, 'message' => 'Rabatt erfolgreich erstellt']);
} catch (PDOException $e) {
    error_log('Fehler beim Erstellen des Rabatts: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Fehler beim Erstellen des Rabatts: ' . $e->getMessage()]);
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
